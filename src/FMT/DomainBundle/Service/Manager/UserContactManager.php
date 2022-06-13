<?php

namespace FMT\DomainBundle\Service\Manager;

use Exception;
use FMT\DataBundle\Entity\Campaign;
use FMT\DataBundle\Entity\CampaignContact;
use FMT\DataBundle\Entity\User;
use FMT\DataBundle\Entity\UserContact;
use FMT\DataBundle\Repository\UserContactRepository;
use FMT\DomainBundle\Service\CampaignManagerInterface;
use FMT\DomainBundle\Service\UserContactManagerInterface;
use FMT\InfrastructureBundle\Helper\LogHelper;
use FMT\InfrastructureBundle\Helper\NotificationHelper;
use FOS\UserBundle\Model\UserInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

/**
 * Class UserContactManager
 * @package FMT\DomainBundle\Service\Manager
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UserContactManager extends EventBasedManager implements UserContactManagerInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EngineInterface
     */
    private $parser;

    /**
     * @var UserContactRepository
     */
    private $userContactRepository;

    /**
     * @var CampaignManagerInterface
     */
    private $campaignManager;

    /**
     * UserContactManager constructor.
     * @param UserContactRepository $userContactRepository
     * @param CampaignManagerInterface $campaignManager
     * @param LoggerInterface $logger
     * @param EngineInterface $parser
     */
    public function __construct(
        UserContactRepository $userContactRepository,
        CampaignManagerInterface $campaignManager,
        LoggerInterface $logger,
        EngineInterface $parser
    ) {
        $this->logger = $logger;
        $this->parser = $parser;
        $this->userContactRepository = $userContactRepository;
        $this->campaignManager = $campaignManager;
    }

    /**
     * @param UserContact $contact
     * @param User $student
     * @param $personalNote
     * @throws Exception
     */
    public function inviteContactToCurrentCampaign(UserContact $contact, User $student, $personalNote)
    {
        try {
            $currentCampaign = $student->getActiveOrUnstartedCampaign();

            $this->addContactToCampaign($contact, $currentCampaign);

            $this->sendCampaignInvitationToContact($student, $contact, $currentCampaign, $personalNote);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
            throw $exception;
        }
    }

    public function inviteAllContactsToCurrentCampaign(User $student) {
        try {
            $currentCampaign = $student->getActiveOrUnstartedCampaign();

            $studentContacts = $student->getContacts()->getValues();

            $campaignContacts = $currentCampaign->getContacts()->getValues();

            $studentContactsIds = array_map(
                function (UserContact $contact) {
                    return $contact->getId();
                },
                $studentContacts
            );

            $campaignContactsIds = array_map(
                function (CampaignContact $contact) {
                    return $contact->getContact()->getId();
                },
                $campaignContacts
            );

            $contactsIdsDiff = array_diff($studentContactsIds, $campaignContactsIds);

            foreach ($studentContacts as $studentContact) {
                if (in_array($studentContact->getId(), $contactsIdsDiff)) {
                    $this->addContactToCampaign($studentContact, $currentCampaign);

                    $this->sendCampaignInvitationToContact($student, $studentContact, $currentCampaign, null);
                }
            }

            $this->campaignManager->updateMassMailedCalled($currentCampaign);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
            throw $exception;
        }
    }

    /**
     * @param UserContact $contact
     * @return mixed|void
     * @throws Exception
     */
    public function removeContact(UserContact $contact)
    {
        try {
            $this->userContactRepository->remove($contact);
            $this->userContactRepository->save();
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
            throw $exception;
        }
    }

    /**
     * @param UserContact $userContact
     * @param Campaign $campaign
     * @return CampaignContact
     */
    public function addContactToCampaign(UserContact $userContact, Campaign $campaign)
    {
        LogHelper::debug("Adding contact to the campaign #%d", $campaign->getId());

        return $this->campaignManager->assignContact($campaign, $userContact);
    }

    /**
     * @param UserInterface $student
     * @param UserContact $contact
     * @param Campaign $campaign
     * @param $personalNote
     */
    protected function sendCampaignInvitationToContact(
        UserInterface $student,
        UserContact $contact,
        Campaign $campaign,
        $personalNote
    ) {
        if (!$campaign->isStarted()) {
            $message = $this->parser->render('@Public/emails/campaign_invitation.email.twig', [
                'contact' => $contact,
                'student' => $student,
                'campaign' => $campaign,
                'personalNote' => $personalNote,
            ]);
        } else {
            $message = $this->parser->render('@Public/emails/donor_invitation.email.twig', [
                'donor' => $contact->getDonor(),
                'student' => $student,
                'personalNote' => $personalNote,
                'isCampaignInvitation' => true,
            ]);
        }

        $recipient = $contact->getDonor()->getEmail();
        NotificationHelper::submitFromTemplate($message, $recipient);
    }
}
