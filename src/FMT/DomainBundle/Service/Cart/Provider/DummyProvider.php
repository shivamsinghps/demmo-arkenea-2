<?php

namespace FMT\DomainBundle\Service\Cart\Provider;

use FMT\DataBundle\Entity\Order;
use FMT\DomainBundle\Service\CartProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class DummyProvider
 * @package FMT\DomainBundle\Service\Cart\Provider
 */
class DummyProvider implements CartProviderInterface
{
    /**
     * @inheritdoc
     */
    public function supports(TokenInterface $token)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getCart(TokenInterface $token)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function createCart(TokenInterface $token): Order
    {
        $cart = new Order();
        $cart
            ->setStatus(Order::STATUS_CART)
            ->setZeroCartPrices()
        ;

        return $cart;
    }
}
