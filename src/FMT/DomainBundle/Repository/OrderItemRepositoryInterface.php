<?php

declare(strict_types=1);

namespace FMT\DomainBundle\Repository;

use DateTime;
use FMT\DataBundle\Entity\OrderItem;
use Generator;

/**
 * Interface OrderItemRepositoryInterface
 * @package FMT\DomainBundle\Repository
 */
interface OrderItemRepositoryInterface extends RepositoryInterface
{
    /**
     * @param DateTime $updatedByTime
     *
     * @return OrderItem[]
     */
    public function findAllUpdatedBy(DateTime $updatedByTime): array;

    /**
     * @param string $returnWindowPeriod for example: "7 days"
     *
     * @return OrderItem[][]|Generator
     */
    public function findChunkInReturnWindow(string $returnWindowPeriod, int $chunkSize): Generator;

    /**
     * @inheritdoc
     */
    public function findReturn();
}
