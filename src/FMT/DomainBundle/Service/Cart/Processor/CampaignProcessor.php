<?php

namespace FMT\DomainBundle\Service\Cart\Processor;

use FMT\DataBundle\Entity\Order;
use FMT\DataBundle\Entity\OrderItem;
use FMT\DomainBundle\Service\CartProcessorInterface;

/**
 * Class CampaignProcessor
 * @package FMT\DomainBundle\Service\Cart\Processor
 */
class CampaignProcessor implements CartProcessorInterface
{
    /**
     * @inheritdoc
     */
    public function supports(Order $cart)
    {
        return $cart->getStatus() === Order::STATUS_CART;
    }

    /**
     * Set order-wide link to the Campaign entity
     *
     * @param Order $cart
     * @return mixed|void
     */
    public function process(Order $cart)
    {
        // set link to campaign for the order
        if ($cart->getItems()->count() > 0) {
            /** @var OrderItem $firstOrderItem */
            $firstOrderItem = $cart->getItems()->first();
            $cart->setCampaign($firstOrderItem->getBook()->getCampaign());

            return;
        }

        // unlink cart from the campaign if there is no products in the cart
        $cart->setCampaign(null);
    }
}
