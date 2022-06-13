<?php

namespace FMT\DomainBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class CartCheckoutEvent
 * @package FMT\DomainBundle\Event
 */
class CartCheckoutEvent extends Event
{
    const CHECKOUT_COMPLETED = 'fmt.checkout_completed';
    const CHECKOUT_FAILED = 'fmt.checkout_failed';

    private $shopperId;

    private $result;

    /**
     * CartCheckout constructor.
     * @param string $shopperId
     * @param array $result
     */
    public function __construct(string $shopperId, array $result)
    {
        $this->shopperId = $shopperId;
        $this->result = $result;
    }

    /**
     * @return string
     */
    public function getShopperId()
    {
        return $this->shopperId;
    }

    /**
     * @return array
     */
    public function getResult()
    {
        return $this->result;
    }
}
