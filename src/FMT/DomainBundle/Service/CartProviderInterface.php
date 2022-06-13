<?php

namespace FMT\DomainBundle\Service;

use Doctrine\ORM\NonUniqueResultException;
use FMT\DataBundle\Entity\Order;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Interface CartProviderInterface
 * @package FMT\DomainBundle\Service
 */
interface CartProviderInterface
{
    /**
     * @param TokenInterface $token
     * @return bool
     */
    public function supports(TokenInterface $token);

    /**
     * @param TokenInterface $token
     * @return Order|null
     * @throws NonUniqueResultException
     */
    public function getCart(TokenInterface $token);

    /**
     * @param TokenInterface $token
     * @return Order
     */
    public function createCart(TokenInterface $token): Order;
}
