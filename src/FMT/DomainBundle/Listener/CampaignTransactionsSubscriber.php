<?php

namespace FMT\DomainBundle\Listener;

use FMT\DomainBundle\Event\TransactionEvent;
use FMT\DomainBundle\Service\CampaignManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class CampaignTransactionsSubscriber
 * @package FMT\DomainBundle\Listener
 */
class CampaignTransactionsSubscriber implements EventSubscriberInterface
{
    /**
     * @var CampaignManagerInterface
     */
    private $campaignManager;

    /**
     * CampaignTransactionsSubscriber constructor.
     * @param CampaignManagerInterface $campaignManager
     */
    public function __construct(CampaignManagerInterface $campaignManager)
    {
        $this->campaignManager = $campaignManager;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            TransactionEvent::TRANSACTION_COMPLETED => 'updateTotalsByTransactions',
        ];
    }

    /**
     * @param TransactionEvent $event
     */
    public function updateTotalsByTransactions(TransactionEvent $event)
    {
        $campaign = $event->getTransaction()->getCampaign();
        $this->campaignManager->updateTotalsByTransactions($campaign);
    }
}
