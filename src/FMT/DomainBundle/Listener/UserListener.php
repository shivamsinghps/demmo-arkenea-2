<?php

namespace FMT\DomainBundle\Listener;

use Exception;
use FMT\DomainBundle\Service\Cart\NebookService;
use FMT\DataBundle\Entity\User;
use FMT\DomainBundle\Event\UserEvent;
use FMT\InfrastructureBundle\Helper\NotificationHelper;
use FOS\UserBundle\Model\UserInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class UserListener
 * @package FMT\PublicBundle\Listener
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class UserListener
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var NebookService
     */
    private $nebookService;

    /**
     * @var EngineInterface
     */
    private $parser;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * UserListener constructor.
     * @param LoggerInterface $logger
     * @param NebookService $nebookService
     */
    public function __construct(LoggerInterface $logger, NebookService $nebookService)
    {
        $this->logger = $logger;
        $this->nebookService = $nebookService;
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
     * @param EngineInterface $engine
     * @required
     */
    public function setEngine(EngineInterface $engine)
    {
        $this->parser = $engine;
    }

    /**
     * @param UserEvent $event
     * @return bool|null
     */
    public function onUserProfileUpdated(UserEvent $event)
    {
        $user = $event->getUser();

        if (!$user instanceof User || !$user->isStudent()) {
            return false;
        }

        try {
            if ($user->getNebookId()) {
                $this->nebookService->updateShopper($user);
            } else {
                $this->nebookService->createShopper($user);
            }

            return true;
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            $this->logger->debug($user);
        }

        return false;
    }

    /**
     * @param UserEvent $event
     */
    public function onContactSignupInitiated(UserEvent $event)
    {
        $donor = $event->getUser();

        if (!$donor instanceof UserInterface || !$donor->isAnyDonor()) {
            return;
        }

        try {
            $message = $this->parser->render('@Public/emails/donor_invitation.email.twig', [
                'donor' => $donor,
                'student' => $this->tokenStorage->getToken()->getUser(),
            ]);
            $recipient = $donor->getProfile()->getEmail();
            NotificationHelper::submitFromTemplate($message, $recipient);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
    }
}
