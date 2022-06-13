<?php

namespace FMT\DataBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @var UniqueEntity
 *
 * @ORM\Table(
 *     name="user",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="IX_unique_login", columns={"login"})},
 *     indexes={@ORM\Index(name="FK_user_profile", columns={"profile_id"})}
 * )
 * @ORM\Entity(repositoryClass="FMT\DataBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(
 *     fields={"login"},
 *     message="fmt.unique_email.error", groups={"Default"})
 * @UniqueEntity(
 *     fields={"login"},
 *     message="fmt.unique_email.reset_request", groups={"registration"})
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class User extends BaseUser implements EntityInterface, EquatableInterface, MinimalUserInterface
{
    use TimestampableEntity;

    const ROLE_STUDENT = 'ROLE_STUDENT';
    const ROLE_DONOR = 'ROLE_DONOR';
    const ROLE_INCOMPLETE_STUDENT = 'ROLE_INCOMPLETE_STUDENT';
    const ROLE_INCOMPLETE_DONOR = 'ROLE_INCOMPLETE_DONOR';

    const DELETED_USER_DELIMITER_MARK = '___';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var UserProfile
     *
     * @ORM\OneToOne(targetEntity="UserProfile", inversedBy="user", cascade={"persist", "remove"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="profile_id", referencedColumnName="id")
     * })
     */
    private $profile;

    /**
     * @var string
     *
     * @ORM\Column(name="login", type="string", length=255, nullable=false)
     */
    private $login;

    /**
     * @var Campaign[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Campaign", mappedBy="user", cascade={"persist"})
     * @ORM\OrderBy({"endDate" = "DESC"})
     */
    private $campaigns;

    /**
     * @var UserTransaction[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="UserTransaction", mappedBy="sender", cascade={"persist"})
     */
    private $transactions;

    /**
     * @var UserContact[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="UserContact", mappedBy="student", cascade={"persist"})
     */
    private $contacts;

    /**
     * @var string
     *
     * @ORM\Column(name="nebook_id", type="string", length=255, nullable=true)
     */
    private $nebookId;

    /**
     * @var UserStatistic|null
     *
     * @ORM\OneToOne(
     *     targetEntity="\FMT\DataBundle\Entity\UserStatistic",
     *      inversedBy="user",
     *      cascade={"persist", "remove"}
     *     )
     */
    private $statistic;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->profile = new UserProfile();
        $this->profile->setUser($this);

        $this->campaigns = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->contacts = new ArrayCollection();

        $this->statistic = new UserStatistic();
        $this->statistic->setUser($this);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return UserProfile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @param UserProfile $profile
     * @return $this
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;

        if ($this->profile) {
            $this->profile->syncFromUser();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param $login
     * @return $this
     */
    public function setLogin($login)
    {
        $this->login = $login;
        $this->email = $login;
        $this->username = $login;
        $this->usernameCanonical = $login;
        $this->emailCanonical = $login;

        if ($this->profile) {
            $this->profile->syncFromUser();
        }

        return $this;
    }

    /**
     * @param string $email
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setEmail($email)
    {
        /**
         * NOTE: Email field should be in sync with login
         */
        return $this;
    }

    /**
     * @param string $username
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setUsername($username)
    {
        /**
         * NOTE: User name field should be in sync with login
         */
        return $this;
    }

    /**
     * @return bool
     */
    public function isStudent()
    {
        return $this->hasRole(self::ROLE_STUDENT);
    }

    /**
     * @return bool
     */
    public function isIncompleteStudent()
    {
        return $this->hasRole(self::ROLE_INCOMPLETE_STUDENT);
    }

    /**
     * @return bool
     */
    public function isActiveStudent()
    {
        if (!$this->isStudent()) {
            return false;
        }

        return $this->isEnabled() && $this->isAccountNonExpired() && $this->isAccountNonLocked();
    }

    /**
     * @return bool
     */
    public function isDonor()
    {
        return $this->hasRole(self::ROLE_DONOR);
    }

    /**
     * @return bool
     */
    public function isIncompleteDonor()
    {
        return $this->hasRole(self::ROLE_INCOMPLETE_DONOR);
    }

    /**
     * @return bool
     */
    public function isAnyStudent()
    {
        return $this->hasRole(User::ROLE_STUDENT) || $this->hasRole(User::ROLE_INCOMPLETE_STUDENT);
    }

    /**
     * @return bool
     */
    public function isAnyDonor()
    {
        return $this->hasRole(User::ROLE_DONOR) || $this->hasRole(User::ROLE_INCOMPLETE_DONOR);
    }

    /**
     * @return bool
     */
    public function isCompleted()
    {
        return $this->hasRole(User::ROLE_DONOR) || $this->hasRole(User::ROLE_STUDENT);
    }

    /**
     * @return bool
     */
    public function isRegistered()
    {
        $hasAnyRole = $this->isAnyDonor() || $this->isAnyStudent();
        return $this->isEnabled() && $hasAnyRole;
    }

    /**
     * @param UserInterface $user
     * @return bool
     */
    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof User) {
            return false;
        }

        return $this->password == $user->getPassword();
    }

    /**
     * @return Campaign[]|ArrayCollection
     */
    public function getCampaigns()
    {
        return $this->campaigns;
    }

    /**
     * @return Campaign|null
     */
    public function getUnfinishedCampaign()
    {
        foreach ($this->campaigns as $campaign) {
            if (!$campaign->isFinished()) {
                return $campaign;
            }
        }

        return null;
    }

    /**
     * @return Campaign|null
     */
    public function getActiveOrUnstartedCampaign()
    {
        foreach ($this->campaigns as $campaign) {
            if ($campaign->isActive() || !$campaign->isStarted() ) {
                return $campaign;
            }
        }

        return null;
    }

    /**
     * @param Campaign $campaign
     * @return $this
     */
    public function addCampaign(Campaign $campaign)
    {
        if (!$this->campaigns->contains($campaign)) {
            $this->campaigns->add($campaign);

            $campaign->setUser($this);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function hasUnfinishedCampaign()
    {
        return (bool) $this->getUnfinishedCampaign();
    }

    /**
     * @return ArrayCollection|UserTransaction[]
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * @param UserTransaction $transaction
     * @return $this
     */
    public function addTransaction(UserTransaction $transaction)
    {
        $existing = $this->transactions->filter(function (UserTransaction $item) use ($transaction) {
            return $transaction->isEqualTo($item);
        });

        if ($existing->isEmpty()) {
            if (!$transaction->getSender()) {
                $transaction->setSender($this);
            }
            $this->transactions->add($transaction);
        }

        return $this;
    }

    /**
     * @return ArrayCollection|UserContact[]
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * @param User $user
     * @return UserContact|null
     */
    public function findContact(User $user)
    {
        $id = $user->getId();
        $exists = $this->contacts->filter(function (UserContact $item) use ($id) {
            return $item->getDonor() && $item->getDonor()->getId() == $id;
        });

        return $exists->isEmpty() ? null : $exists->first();
    }

    /**
     * @param User $user
     * @return bool
     */
    public function hasContact(User $user)
    {
        return $this->findContact($user) !== null;
    }

    /**
     * @param string|null $email
     * @return UserContact|null
     */
    public function findContactByEmail($email)
    {
        if (!$email) {
            return null;
        }

        $exists = $this->contacts->filter(function (UserContact $item) use ($email) {
            return $item->getDonor() && $item->getDonor()->getLogin() == $email;
        });

        return $exists->isEmpty() ? null : $exists->first();
    }

    /**
     * @param string|null $email
     * @return bool
     */
    public function hasContactByEmail($email)
    {
        if (!$email) {
            return false;
        }

        return $this->findContactByEmail($email) !== null;
    }

    /**
     * @param User $user
     * @return UserContact
     */
    public function addContact(User $user)
    {
        $result = new UserContact();
        $result->setDonor($user);
        $result->setStudent($this);
        $result->setFirstName($user->getProfile()->getFirstName());
        $result->setLastName($user->getProfile()->getLastName());

        $this->contacts->add($result);

        return $result;
    }

    /**
     * @return $this
     */
    public function syncFromProfile()
    {
        if ($this->profile) {
            $email = $this->profile->getEmail();

            $this->login = $email;
            $this->email = $email;
            $this->username = $email;
            $this->usernameCanonical = $email;
            $this->emailCanonical = $email;
        }

        return $this;
    }

    /**
     * @return UserMajor
     */
    public function getMajor()
    {
        return $this->profile->getMajor();
    }

    /**
     * @return string
     */
    public function getNebookId()
    {
        return $this->nebookId;
    }

    /**
     * @param string $nebookId
     * @return $this
     */
    public function setNebookId($nebookId)
    {
        $this->nebookId = $nebookId;

        return $this;
    }

    /**
     * @return UserStatistic|null
     */
    public function getStatistic()
    {
        return $this->statistic;
    }

    /**
     * @param UserStatistic|null $statistic
     * @return User
     */
    public function setStatistic(UserStatistic $statistic): User
    {
        $this->statistic = $statistic;
        if ($statistic->getUser() !== $this) {
            $statistic->setUser($this);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFirstName(): ?string
    {
        return $this->profile->getFirstName();
    }

    /**
     * @inheritDoc
     */
    public function setFirstName($firstName)
    {
        $this->profile->setFirstName($firstName);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLastName(): ?string
    {
        return $this->profile->getLastName();
    }

    /**
     * @inheritDoc
     */
    public function setLastName($lastName)
    {
        $this->profile->setLastName($lastName);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFullName(): ?string
    {
        return $this->profile->getFullName();
    }
}
