<?php

namespace FMT\DomainBundle\Service\Cart\Processor;

use FMT\DataBundle\Entity\Order;
use FMT\DomainBundle\Service\Cart\NebookService;
use FMT\DomainBundle\Service\CartProcessorInterface;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Exception;

/**
 * Class NebookProcessor
 * @package FMT\DomainBundle\Service\Cart\Processor
 */
class NebookProcessor implements CartProcessorInterface
{
    /**
     * @var NebookService
     */
    private $nebookService;

    /**
     * UserCartProvider constructor.
     * @param NebookService $nebookService
     */
    public function __construct(NebookService $nebookService)
    {
        $this->nebookService = $nebookService;
    }

    /**
     * @inheritdoc
     */
    public function supports(Order $cart)
    {
        return $cart->getStatus() === Order::STATUS_CART;
    }

    /**
     * Requests Nebook API to calculate price, tax and shipping price
     *
     * @param Order $cart
     * @throws Exception
     */
    public function process(Order $cart)
    {
        $cart->resetPrice();

        if ($cart->getItems()->count() > 0) {
            $summary = $this->nebookService->getOrderSummary($cart);
            $taxAmount = $this->nebookService->getTaxAmount($cart);

            $cart
                ->setPrice($summary->getSubTotal())
                ->setTax($taxAmount)
                ->setShipping($summary->getShippingTotal())
            ;
        }

        $cart->recalculateTotal();
    }
}
