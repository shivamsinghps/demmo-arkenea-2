<?php

declare(strict_types=1);

namespace FMT\DomainBundle\Repository;

use FMT\DataBundle\Entity\BookstoreTransfer;

/**
 * Interface BookstoreTransferRepositoryInterface
 */
interface BookstoreTransferRepositoryInterface extends RepositoryInterface
{
    /**
     * @return BookstoreTransfer|null
     */
    public function findLastCreated(): ?BookstoreTransfer;

    /**
     * @return BookstoreTransfer[]
     */
    public function findWithoutParentByStatus(string $status): array;
}
