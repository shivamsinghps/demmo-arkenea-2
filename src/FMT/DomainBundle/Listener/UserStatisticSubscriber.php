<?php

namespace FMT\DomainBundle\Listener;

use FMT\DataBundle\Entity\User;
use FMT\DataBundle\Entity\UserTransaction;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use FMT\DomainBundle\Event\UserEvent;
use FMT\DomainBundle\Event\TransactionEvent;
use FMT\DomainBundle\Repository\UserStatisticRepositoryInterface;

/**
 * Class UserStatisticSubscriber
 * @package FMT\PublicBundle\Subscriber
 */
class UserStatisticSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserStatisticRepositoryInterface
     */
    private $userStatisticRepository;

    /**
     * UserStatisticSubscriber constructor.
     * @param UserStatisticRepositoryInterface $userStatisticRepository
     */
    public function __construct(UserStatisticRepositoryInterface $userStatisticRepository)
    {
        $this->userStatisticRepository = $userStatisticRepository;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            TransactionEvent::TRANSACTION_COMPLETED => 'updateTransactionStatistic',
            // todo add event subscriber to "book purchasing"
        ];
    }

    /**
     * @param TransactionEvent $event
     */
    public function updateTransactionStatistic(TransactionEvent $event)
    {
        $sender = $event->getTransaction()->getSender();
        $recipient = $event->getTransaction()->getRecipient();
        if ($sender instanceof User) {
            $this->userStatisticRepository->updateStudentsFounded($sender);
            $this->userStatisticRepository->updateAmountFounded($sender);
        }

        $type = $event->getTransaction()->getType();
        if ($type == UserTransaction::TXN_DONATION || $type == UserTransaction::TXN_BOOK_PURCHASE) {
            $this->userStatisticRepository->updateAmountDonatedTo($recipient);
        } 
    }

    /**
     * @param UserEvent $event
     */
    public function updateBooksPurchased(UserEvent $event)
    {
        $this->userStatisticRepository->updateBooksPurchasedFor($event->getUser());
    }
}
