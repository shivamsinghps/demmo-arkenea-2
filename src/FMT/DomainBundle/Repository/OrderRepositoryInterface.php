<?php

namespace FMT\DomainBundle\Repository;

use Doctrine\ORM\NonUniqueResultException;
use FMT\DataBundle\Entity\Order;
use Generator;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Interface OrderRepositoryInterface
 * @package FMT\DomainBundle\Repository
 */
interface OrderRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $token
     * @return Order|null
     * @throws NonUniqueResultException
     */
    public function getAnonymousCart(string $token);

    /**
     * @param UserInterface $user
     * @return Order|null
     * @throws NonUniqueResultException
     */
    public function getUserCart(UserInterface $user);

    /**
     * @param string $status
     * @param int    $chunkSize
     *
     * @return Generator|Order[][]
     */
    public function findChunkWithStatus(string $status, int $chunkSize): Generator;

    /**
     * @return Generator|Order[][]
     */
    public function findCheckout();
}
