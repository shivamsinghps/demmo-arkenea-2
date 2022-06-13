<?php

declare(strict_types=1);

namespace FMT\DataBundle\Repository;

use FMT\DataBundle\Entity\BookstoreTransfer;
use FMT\DomainBundle\Repository\BookstoreTransferRepositoryInterface;

/**
 * Class BookstoreTransferRepository
 */
class BookstoreTransferRepository extends DoctrineRepository implements BookstoreTransferRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function findLastCreated(): ?BookstoreTransfer
    {
        $qb = $this->createQueryBuilder('BookstoreTransfer');
        $qb
            ->addOrderBy('BookstoreTransfer.createdAt', 'DESC')
            ->setMaxResults(1)
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @inheritDoc
     */
    public function findWithoutParentByStatus(string $status): array
    {
        $qb = $this->createQueryBuilder('BookstoreTransfer');
        $qb
            ->andWhere($qb->expr()->isNull('BookstoreTransfer.parent'))
            ->andWhere('BookstoreTransfer.status = :status')
            ->setParameter('status', $status)
        ;

        return $qb->getQuery()->getResult();
    }
}
