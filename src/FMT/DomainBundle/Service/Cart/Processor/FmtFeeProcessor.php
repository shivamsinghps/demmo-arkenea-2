<?php

namespace FMT\DomainBundle\Service\Cart\Processor;

use FMT\DataBundle\Entity\Order;
use FMT\DomainBundle\Repository\OrderRepositoryInterface;
use FMT\DomainBundle\Service\CartProcessorInterface;
use FMT\DomainBundle\Service\CartProviderInterface;
use FMT\DomainBundle\Type\Payment\ChargeCalculatorInterface;
use FMT\DomainBundle\Type\Payment\FMT;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class FmtFeeProcessor
 * @package FMT\DomainBundle\Service\Cart\Processor
 */
class FmtFeeProcessor implements CartProcessorInterface
{
    /**
     * @var ChargeCalculatorInterface
     */
    private $calculator;

    /**
     * FmtFeeProcessor constructor.
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
     * Calculate FMT Fee for transaction
     *
     * @param Order $cart
     * @return mixed|void
     */
    public function process(Order $cart)
    {
        $fmtFee = $this->calculator->fee($cart->getTotalForCheckout());

        $cart->setFmtFee($fmtFee);
        $cart->recalculateTotal();
    }
}
