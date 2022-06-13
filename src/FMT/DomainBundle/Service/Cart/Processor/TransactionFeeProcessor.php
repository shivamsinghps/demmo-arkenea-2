<?php

namespace FMT\DomainBundle\Service\Cart\Processor;

use FMT\DataBundle\Entity\Order;
use FMT\DomainBundle\Service\CartProcessorInterface;
use FMT\DomainBundle\Type\Payment\ChargeCalculatorInterface;

/**
 * Class TransactionFeeProcessor
 * @package FMT\DomainBundle\Service\Cart\Processor
 */
class TransactionFeeProcessor implements CartProcessorInterface
{
    /**
     * @var ChargeCalculatorInterface
     */
    private $calculator;

    /**
     * TransactionFeeProcessor constructor.
     * @param ChargeCalculatorInterface $calculator
     */
    public function __construct(ChargeCalculatorInterface $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * @inheritdoc
     */
    public function supports(Order $cart)
    {
        return $cart->getStatus() === Order::STATUS_CART;
    }

    /**
     * Calculates payment system fee
     *
     * @param Order $cart
     * @return mixed|void
     */
    public function process(Order $cart)
    {
        $cart->setTransactionFee(0);

        if ($cart->getItems()->count() > 0) {
            $transactionFee = $this->calculator->fee(
                $cart->getTotalForCheckout() + $cart->getFmtFee()
            );

            $cart->setTransactionFee($transactionFee);
        }

        $cart->recalculateTotal();
    }
}
