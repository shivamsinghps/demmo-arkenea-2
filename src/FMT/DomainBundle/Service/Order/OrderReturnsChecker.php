<?php

declare(strict_types=1);

namespace FMT\DomainBundle\Service\Order;

use FMT\DomainBundle\Event\OrderItemReturnEvent;
use FMT\DomainBundle\Repository\OrderItemRepositoryInterface;
use FMT\DomainBundle\Service\OrderReturnsCheckerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class OrderReturnsChecker
 * @package FMT\DomainBundle\Service\Order
 */
class OrderReturnsChecker implements OrderReturnsCheckerInterface
{
    /** @var string  */
    private $returnWindow;

    /** @var int */
    private $chunkSize;

    /** @var OrderItemRepositoryInterface  */
    private $repository;

    /** @var EventDispatcherInterface  */
    private $eventDispatcher;

    public function __construct(
        string $returnWindow,
        int $chunkSize,
        OrderItemRepositoryInterface $orderItemRepository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->returnWindow = $returnWindow;
        $this->chunkSize = $chunkSize;
        $this->repository = $orderItemRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritdoc
     */
    public function check(): void
    {
        foreach ($this->repository->findChunkInReturnWindow($this->returnWindow, $this->chunkSize) as $orderItems) {
            foreach ($orderItems as $orderItem) {
                $event = new OrderItemReturnEvent($orderItem);
                $this->eventDispatcher->dispatch(OrderItemReturnEvent::EVENT_RETURN, $event);
            }

            $this->repository->getEm()->clear();
        }
    }
}
