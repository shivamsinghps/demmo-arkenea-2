<?php

declare(strict_types=1);

namespace FMT\DomainBundle\Listener;

use FMT\DomainBundle\Event\OrderItemReturnEvent;
use FMT\DomainBundle\Exception\InvalidReturnOrderItemException;
use FMT\DomainBundle\Service\CampaignManagerInterface;
use FMT\DomainBundle\Service\PaymentManagerInterface;

/**
 * Class ManageReturnsFoundsListener
 * @package FMT\DomainBundle\Listener
 */
class ManageReturnFoundsListener
{
    /** @var PaymentManagerInterface */
    private $paymentManager;

    /** @var CampaignManagerInterface */
    private $campaignManager;

    public function __construct(
        PaymentManagerInterface $paymentManager,
        CampaignManagerInterface $campaignManager
    ) {
        $this->paymentManager = $paymentManager;
        $this->campaignManager = $campaignManager;
    }

    /**
     * @param OrderItemReturnEvent $event
     *
     * @throws InvalidReturnOrderItemException
     */
    public function onOrderItemReturned(OrderItemReturnEvent $event): void
    {
        $orderItem = $event->getOrderItem();
        $this->paymentManager->sendRefundFromOrderItemReturn($orderItem);
        $this->campaignManager->updateTotalsByTransactions($orderItem->getOrder()->getCampaign());

    }
}
