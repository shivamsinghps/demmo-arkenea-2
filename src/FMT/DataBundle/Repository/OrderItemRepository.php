<?php

declare(strict_types=1);

namespace FMT\DataBundle\Repository;

use DateInterval;
use DateTime;
use FMT\DataBundle\Entity\OrderItem;
use FMT\DataBundle\Traits\ChunkedQueryBuilder;
use FMT\DomainBundle\Repository\OrderItemRepositoryInterface;
use Generator;

class OrderItemRepository extends DoctrineRepository implements OrderItemRepositoryInterface
{
    use ChunkedQueryBuilder;

    /**
     * @inheritdoc
     */
    public function findAllUpdatedBy(DateTime $updatedByTime): array
    {
        return $this->createQueryBuilder('OrderItem')
            ->andWhere('OrderItem.updatedAt > :updatedByTime')
            ->setParameter('updatedByTime', $updatedByTime)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @inheritdoc
     */
    public function findChunkInReturnWindow(string $returnWindowPeriod, int $chunkSize): Generator
    {
        $updatedAt = (new DateTime())->sub(DateInterval::createFromDateString($returnWindowPeriod))->setTime(0, 0);
        $countChunks = ceil($this->total() / $chunkSize);

        for ($i = 0; $i < $countChunks; $i++) {
            $qb = $this->getChunkedQueryBuilder($this->createQueryBuilder('OrderItem'), $chunkSize, $chunkSize * $i);

            yield $qb
                ->leftJoin('OrderItem.logs', 'LogOrderItem')
                ->andWhere('OrderItem.status = :status')
                ->andWhere('LogOrderItem.loggedAt >= :updatedAt')
                ->setParameter('status', OrderItem::STATUS_PRE_RETURNED)
                ->setParameter('updatedAt', $updatedAt)
                ->getQuery()
                ->getResult();
        }
    }

    /**
     * @inheritdoc
     */
    public function findReturn()
    {
        return $this->createQueryBuilder('OrderItem')
            ->where('OrderItem.unprocessedAmount != 0')
            ->andWhere('OrderItem.status = :status')
            ->setParameter('status', OrderItem::STATUS_RETURNED)
            ->getQuery()
            ->getResult();
    }
}
