<?php

namespace FMT\DomainBundle\Service\Cart\Provider;

use FMT\DataBundle\Entity\Order;
use FMT\DomainBundle\Repository\OrderRepositoryInterface;
use FMT\DomainBundle\Service\CartProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserProvider
 * @package FMT\DomainBundle\Service\Cart\Provider
 */
class UserProvider implements CartProviderInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * UserCartProvider constructor.
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @inheritdoc
     */
    public function supports(TokenInterface $token)
    {
        return $token->getUser() instanceof UserInterface;
    }

    /**
     * @inheritdoc
     */
    public function getCart(TokenInterface $token)
    {
        $user = $token->getUser();

        return $this->orderRepository->getUserCart($user);
    }

    /**
     * @inheritdoc
     */
    public function createCart(TokenInterface $token): Order
    {
        $cart = new Order();
        $cart
            ->setStatus(Order::STATUS_CART)
            ->setUser($token->getUser())
            ->setZeroCartPrices()
        ;

        return $cart;
    }
}
