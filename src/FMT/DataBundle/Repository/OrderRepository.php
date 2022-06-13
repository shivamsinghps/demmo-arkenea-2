<?php

namespace FMT\DataBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use FMT\DataBundle\Entity\Order;
use FMT\DataBundle\Traits\ChunkedQueryBuilder;
use FMT\DomainBundle\Repository\OrderRepositoryInterface;
use FMT\InfrastructureBundle\Helper\DateHelper;
use Generator;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class OrderRepository
 * @package FMT\DataBundle\Repository
 */
class OrderRepository extends DoctrineRepository implements OrderRepositoryInterface
{
    use ChunkedQueryBuilder;

    /**
     * @inheritdoc
     */
    public function getAnonymousCart(string $token)
    {
        $qb = $this->getCartQueryBuilder();

        return $qb->andWhere('o.anonymousToken = :token')
            ->andWhere('o.user IS NULL')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * @inheritdoc
     */
    public function getUserCart(UserInterface $user)
    {
        $qb = $this->getCartQueryBuilder();

        return $qb->andWhere('o.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * @inheritdoc
     */
    public function findChunkWithStatus(string $status, int $chunkSize): Generator
    {
        $countChunks = ceil($this->total() / $chunkSize);

        for ($i = 0; $i < $countChunks; $i++) {
            yield $this->getChunkedQueryBuilder($this->createQueryBuilder('o'), $chunkSize, $i * $chunkSize)
                ->andWhere('o.status = :status')
                ->setParameter('status', $status)
                ->getQuery()
                ->getResult();
        }
    }

    /**
     * @inheritdoc
     */
    public function findWithStatus(string $status)
    {
        $utcNow = DateHelper::getUtcNow()->sub(new \DateInterval('P60D'));
        return $this->createQueryBuilder('o')
                ->where('o.status = :status')
                ->andWhere('o.updatedAt > :now')
                ->setParameter('status', $status)
                ->setParameter('now', $utcNow)
                ->getQuery()
                ->getResult();

    }

    #region Queries

    /**
     * @return QueryBuilder
     */
    private function getCartQueryBuilder()
    {
        return $this->createQueryBuilder('o')
            ->where('o.status = :cart')
            ->setParameter('cart', Order::STATUS_CART)
            ->orderBy('o.createdAt', 'DESC')
            ->setMaxResults(1)
            ;
    }
    #endregion

    /**
     * @inheritdoc
     */
    public function findCheckout()
    {
        return $this->createQueryBuilder('o')
            ->where('o.unprocessedAmount != 0')
            ->andWhere('o.status = :status')
            ->setParameter('status', Order::STATUS_COMPLETED)
            ->getQuery()
            ->getResult();
    }
}
