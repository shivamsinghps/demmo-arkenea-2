<?php

namespace FMT\DomainBundle\Event;

use FMT\DataBundle\Entity\Order;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class CartActionEvent
 * @package FMT\DomainBundle\Event
 */
class CartActionEvent extends Event
{
    const ADD_PRODUCT = 'fmt.cart.add_product';
    const REMOVE_PRODUCT = 'fmt.cart.remove_product';
    const ESTIMATE_CART = 'fmt.cart.estimate_cart';

    private $cart;

    /**
     * CartCheckout constructor.
     * @param Order $cart
     */
    public function __construct(Order $cart)
    {
        $this->cart = $cart;
    }

    /**
     * @return Order
     */
    public function getCart(): Order
    {
        return $this->cart;
    }
}
