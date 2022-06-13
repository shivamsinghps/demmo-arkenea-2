<?php

declare(strict_types=1);

namespace FMT\DomainBundle\Event;

use FMT\DataBundle\Entity\OrderItem;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class OrderItemReturnEvent
 * @package FMT\DomainBundle\Event
 */
class OrderItemReturnEvent extends Event
{
    public const EVENT_RETURN = 'fmt.order_item.return';

    /** @var OrderItem */
    private $orderItem;

    public function __construct(OrderItem $orderItem)
    {
        $this->orderItem = $orderItem;
    }

    public function getOrderItem(): OrderItem
    {
        return $this->orderItem;
    }
}
