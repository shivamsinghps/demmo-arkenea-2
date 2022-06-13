<?php

namespace FMT\DomainBundle\Service\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use FMT\DataBundle\Entity\User;
use FMT\DataBundle\Entity\UserContact;
use FMT\DataBundle\Entity\UserMajor;
use FMT\DataBundle\Entity\UserProfile;
use FMT\DataBundle\Entity\UserSchool;
use FMT\DataBundle\Model\BaseFilterOptions;
use FMT\DomainBundle\Event\UserEvent;
use FMT\DomainBundle\Repository\UserRepositoryInterface;
use FMT\DomainBundle\Service\CampaignManagerInterface;
use FMT\DomainBundle\Service\Synchronizer\MajorSynchronizer;
use FMT\DomainBundle\Service\Synchronizer\SchoolSynchronizer;
use FMT\DomainBundle\Service\UserManagerInterface;
use FMT\InfrastructureBundle\Helper\LogHelper;
use FOS\UserBundle\Doctrine\UserManager as FOSUserManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use RuntimeException;

/**
 * Class UserManager
 * @package FMT\DomainBundle\Service\Security
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class UserManager extends EventBasedManager implements UserManagerInterface
{
    /** @var FOSUserManager */
    private $manager;

    /** @var MajorSynchronizer */
    private $majorSynchronizer;

    /** @var SchoolSynchronizer */
    private $schoolSynchronizer;

    /** @var UserRepositoryInterface */
    private $userRepository;

    /** @var CampaignManagerInterface */
    private $campaignManager;

    /**
     * @var TokenStorageInterface|null
     */
    private $tokenStorage;

    /** @var TokenGeneratorInterface */
    private $tokenGenerator;

    /**
     * UserManager constructor.
     * @param FOSUserManager $manager
     */
    public function __construct(FOSUserManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param UserRepositoryInterface $repository
     * @required
     */
    public function setUserRepository(UserRepositoryInterface $repository)
    {
        $this->userRepository = $repository;
    }

    /**
     * @param CampaignManagerInterface $manager
     * @required
     */
    public function setCampaignManager(CampaignManagerInterface $manager)
    {
        $this->campaignManager = $manager;
    }

    /**
     * @param MajorSynchronizer $synchronizer
     * @required
     */
    public function setMajorSynchronizer(MajorSynchronizer $synchronizer)
    {
        $this->majorSynchronizer = $synchronizer;
    }

    /**
     * @param SchoolSynchronizer $synchronizer
     * @required
     */
    public function setSchoolSynchronizer(SchoolSynchronizer $synchronizer)
    {
        $this->schoolSynchronizer = $synchronizer;
    }

    /**
     * @param TokenStorageInterface $tokenStorage
     * @required
     */
    public function setTokenStorage(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param TokenGeneratorInterface $generator
     * @required
     */
    public function setTokenGenerator(TokenGeneratorInterface $generator)
    {
        $this->tokenGenerator = $generator;
    }

    /**
     * Returns active student by student ID
     *
     * @param int $id
     * @return User
     */
    public function getActiveStudent($id)
    {
        /** @var User $result */
        $result = $this->manager->findUserBy(["id" => $id]);

        if (empty($result) || !$result->isActiveStudent()) {
            return null;
        }

        return $result;
    }

    /**
     * @param User $user
     * @throws \Exception
     */
    public function confirm(User $user)
    {
        $event = new UserEvent($user);

        $this->dispatch(UserEvent::CONFIRMATION_RECEIVED, $event);

        try {
            if ($user->getId() === null) {
                // TODO: Replace with domain-based exception of corresponding type
                throw new \Exception("Unable to confirm user that is not exist yet");
            }

            $user
                ->setEnabled(true)
                ->setConfirmationToken(null);

            $this->dispatch(UserEvent::CONFIRMATION_SUCCESS, $event);

            $this->manager->updateUser($user);
        } catch (\Exception $exception) {
            $this->dispatch(UserEvent::CONFIRMATION_FAILED, $event);
            throw $exception;
        }
    }

    /**
     * @param User $user
     * @return User
     * @throws RuntimeException
     */
    public function create(User $user)
    {
        $event = new UserEvent($user);

        $this->dispatch(UserEvent::SIGNUP_STARTED, $event);

        try {
            if ($user->getId() !== null) {
                // TODO: Replace with domain-based exception of corresponding type
                throw new RuntimeException("This user already registered");
            }
            //$user->setPassword(base64_encode(uniqid('', true)));
            //$user->setPassword(base64_encode('123456'));
            $user->setPassword('QXJrZW5lYUAxMjM0NQ==');
            $user->setEnabled(true)->setConfirmationToken(null);
            $this->manager->updateUser($user);
        } catch (RuntimeException $exception) {
            $this->dispatch(UserEvent::SIGNUP_FAILED, $event);
            throw $exception;
        }

        $this->dispatch(UserEvent::SIGNUP_SUCCESS, $event);

        return $user;
    }

    /**
     * @param UserInterface $user
     * @return UserInterface
     */
    public function findOrCreateDonorAsContact(UserInterface $user)
    {
        if ($existingDonor = $this->manager->findUserByEmail($user->getEmail())) {

            return $existingDonor;
        }

        $event = new UserEvent($user);

        $this->dispatch(UserEvent::CONTACT_SIGNUP_INITIATED, $event);

        try {
            if ($user->getId() !== null) {
                throw new RuntimeException('This user is already exist');
            }
            $user->setPassword(base64_encode(uniqid('', true)));
            $user->setConfirmationToken($this->tokenGenerator->generateToken());
            $this->manager->updateUser($user);
        } catch (RuntimeException $exception) {
            $this->dispatch(UserEvent::SIGNUP_FAILED, $event);
            throw $exception;
        }

        return $user;
    }

    /**
     * @param User $user
     * @param bool $allowNotEnabled
     * @throws \Exception
     */
    public function update(User $user, bool $allowNotEnabled = false)
    {
        $event = new UserEvent($user);
        try {
            if (!$user->isEnabled() && !$allowNotEnabled) {
                throw new \Exception("You can't change password. User is disabled.");
            }
            $this->dispatch(UserEvent::USER_UPDATED, $event);
            $this->dispatch(UserEvent::USER_PROFILE_UPDATED, $event);
            $this->manager->updateUser($user);
        } catch (\Exception $exception) {
            $this->dispatch(UserEvent::SIGNUP_FAILED, $event);
            throw $exception;
        }
    }

    /**
     * @param bool $forActiveCampaign
     * @return UserMajor[]|ArrayCollection
     * @codeCoverageIgnore
     */
    public function getMajors($forActiveCampaign = false)
    {
        return $this->majorSynchronizer
            ->setForActiveCampaign($forActiveCampaign)
            ->setVisibilityData(
                $this->getVisibilityData()
            )
            ->synchronize();
    }

    /**
     * @return UserSchool[]|array
     * @codeCoverageIgnore
     */
    public function getSchools()
    {
        return $this->schoolSynchronizer->synchronize();
    }

    /**
     * @param User $user
     * @return User
     */
    public function createOrUpdateUser(User $user)
    {
        $result = $this->manager->findUserBy(["login" => $user->getLogin()]);
        if (empty($result)) {
            LogHelper::info("User %s is not found - creating new one", $user->getLogin());

            $result = $user;
            $result->setEnabled(false);
            $this->manager->updateUser($result);
        } else {
            LogHelper::info("User %s already exists", $user->getLogin());
        }

        return $result;
    }

    /**
     * @param User $student
     * @param User $contact
     * @param bool $assignToCampaign
     * @return UserContact
     */
    public function addContact(User $student, User $contact, $assignToCampaign = false)
    {
        if (!$student->isActiveStudent()) {
            throw new \RuntimeException("This user could not attach contacts");
        }

        LogHelper::debug(
            "Creating contact %s %s (%s) for student #%d",
            $contact->getProfile()->getFirstName(),
            $contact->getProfile()->getLastName(),
            $contact->getLogin(),
            $student->getId()
        );

        /** @var User $donor */
        $donor = $this->findOrCreateDonorAsContact($contact);

        $isNew = !($userContact = $student->findContact($donor));
        if ($isNew) {
            $userContact = $student->addContact($donor);
        }

        $userContact->setFirstName($contact->getProfile()->getFirstName());
        $userContact->setLastName($contact->getProfile()->getLastName());

        $this->manager->updateUser($donor);

        if ($isNew) {
            $this->dispatch(UserEvent::USER_CONTACT_ADDED, new UserEvent($student));
        }

        if ($assignToCampaign && ($campaign = $student->getUnfinishedCampaign())) {
            LogHelper::debug("Adding contact to the campaign #%d", $campaign->getId());

            $campaignContact = $this->campaignManager->assignContact($campaign, $userContact);
            $userContact->addCampaignContact($campaignContact);

            $this->userRepository->save($userContact);
        }

        return $userContact;
    }

    /**
     * @param $email
     * @return null|object|User
     * @codeCoverageIgnore
     */
    public function getUserByEmail($email)
    {
        return $this->manager->findUserByEmail($email);
    }

    /**
     * @return User
     */
    public function makeDonor()
    {
        $user = new User();
        $user->setRoles([User::ROLE_INCOMPLETE_DONOR]);
        $user->getProfile()->setVisible(UserProfile::VISIBILITY_NON);

        return $user;
    }

    /**
     * @return User
     */
    public function makeStudent()
    {
        $user = new User();
        $user->setRoles([User::ROLE_INCOMPLETE_STUDENT]);

        return $user;
    }

    /**
     * @param User $user
     * @return User
     */
    public function completeUser(User $user)
    {
        if ($user->isIncompleteDonor()) {
            $user->setRoles([User::ROLE_DONOR]);
        } elseif ($user->isIncompleteStudent()) {
            $user->setRoles([User::ROLE_STUDENT]);
        }

        return $user;
    }

    /**
     * @param BaseFilterOptions $formFilterParams
     * @return QueryBuilder
     */
    public function getDonatedStudentsFiltered(BaseFilterOptions $formFilterParams)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        return $this->userRepository->getDonatedStudentsFiltered($formFilterParams, $user);
    }

    /**
     * @param UserInterface $user
     */
    public function disableAccount(UserInterface $user)
    {
        $newLogin = sprintf('%s%s%s', $user->getEmail(), User::DELETED_USER_DELIMITER_MARK, time());
        $user->setLogin($newLogin);
        $user->setEnabled(false);

        $this->manager->updateUser($user);
    }

    /**
     * @return array
     */
    protected function getVisibilityData()
    {
        $visibility = [UserProfile::VISIBILITY_ALL];

        $token = $this->tokenStorage->getToken();
        if (null !== $token && $token->getUser() instanceof User) {
            $visibility[] = UserProfile::VISIBILITY_REGISTRED;
        }

        return $visibility;
    }
}
