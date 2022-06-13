<?php

declare(strict_types=1);

namespace FMT\DomainBundle\Service\Order;

use Doctrine\ORM\OptimisticLockException;
use FMT\DataBundle\Entity\Order;
use FMT\DataBundle\Entity\OrderItem;
use FMT\DomainBundle\Repository\OrderRepositoryInterface;
use FMT\InfrastructureBundle\Helper\LogHelper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Client;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\OrderItem as ApiOrderItem;

class MonitorOrder
{
    /** @var OrderRepositoryInterface */
    private $repository;

    /** @var Client */
    private $nebookClient;

    /** @var int */
    private $chunkSize;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        Client $nebookClient,
        int $chunkSize
    ) {
        $this->repository = $orderRepository;
        $this->nebookClient = $nebookClient;
        $this->chunkSize = $chunkSize;
    }

    /**
     * @throws OptimisticLockException
     */
    public function monitor(): void
    {
        $orders = $this->repository->findWithStatus(Order::STATUS_COMPLETED);
        foreach ($orders as $order) {
            $this->monitorOrder($order);
        }

        $this->repository->getEm()->flush();
        $this->repository->getEm()->clear();
    }

    /**
     * @param Order $order
     */
    private function monitorOrder(Order $order): void
    {
        $apiOrder = $this->nebookClient->orderGetById($order->getExternalId());
        $orderItemsBySku = [];

        foreach ($order->getItems() as $orderItem) {
            $orderItemsBySku[$orderItem->getSku()] = $orderItem;
        }

        foreach ($apiOrder->getItems() as $apiOrderItem) {
            $sku = $apiOrderItem->getSku();

            if (!array_key_exists($sku, $orderItemsBySku)) {
                LogHelper::critical(
                    (array) sprintf(
                        'In order with id "%s", don\'t existing orderItem with sku "%s"',
                        $order->getId(),
                        $sku
                    )
                );
                
                continue;
            }

            $this->monitorOrderItem($orderItemsBySku[$apiOrderItem->getSku()], $apiOrderItem);
        }
    }

    private function monitorOrderItem(OrderItem $orderItem, ApiOrderItem $apiOrderItem): void
    {
        if ($orderItem->getStatus() == OrderItem::STATUS_RETURNED) {
            return;
        }
        switch (strtoupper($apiOrderItem->getStatus())) {
            case strtoupper(ApiOrderItem::STATUS_CANCELED):
            case strtoupper(ApiOrderItem::STATUS_DELETED):
            case strtoupper(ApiOrderItem::STATUS_RETURNED):
                $orderItem->setStatus(OrderItem::STATUS_PRE_RETURNED);
                return;
            case strtoupper(ApiOrderItem::STATUS_SHIPPED):
                $orderItem->setStatus(OrderItem::STATUS_SHIPPED);
                return;
            case strtoupper(ApiOrderItem::STATUS_SUBMITTED):
                $orderItem->setStatus(OrderItem::STATUS_SUBMITTED);
                return;
        }
    }
}
