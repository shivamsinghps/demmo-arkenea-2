<?php

namespace FMT\DomainBundle\Service\Cart\Provider;

use FMT\DataBundle\Entity\Order;
use FMT\DomainBundle\Repository\OrderRepositoryInterface;
use FMT\DomainBundle\Service\CartProviderInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class AnonymousProvider
 * @package FMT\DomainBundle\Service\Cart\Provider
 */
class AnonymousProvider implements CartProviderInterface
{
    const CART_TOKEN_KEY = 'fmt-cart-anonymous-token';

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var TokenGeneratorInterface
     */
    private $tokenGenerator;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * UserCartProvider constructor.
     * @param OrderRepositoryInterface $orderRepository
     * @param TokenGeneratorInterface $tokenGenerator
     * @param SessionInterface $session
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        TokenGeneratorInterface $tokenGenerator,
        SessionInterface $session
    ) {
        $this->orderRepository = $orderRepository;
        $this->tokenGenerator = $tokenGenerator;
        $this->session = $session;
    }

    /**
     * @inheritdoc
     */
    public function supports(TokenInterface $token)
    {
        return $token->getUser() === 'anon.';
    }

    /**
     * Get Cart by session-based anonymous token
     *
     * @inheritdoc
     */
    public function getCart(TokenInterface $token)
    {
        $token = $this->getCartToken();

        if (is_null($token)) {
            return null;
        }

        return $this->orderRepository->getAnonymousCart($token);
    }

    /**
     * @inheritdoc
     */
    public function createCart(TokenInterface $token): Order
    {
        $token = $this->tokenGenerator->generateToken();
        $this->setCartToken($token);

        $cart = new Order();
        $cart
            ->setAnonymousToken($token)
            ->setStatus(Order::STATUS_CART)
            ->setZeroCartPrices()
        ;

        return $cart;
    }

    #region Internal

    /**
     * @return string|null
     */
    private function getCartToken()
    {
        return $this->session->get(self::CART_TOKEN_KEY);
    }

    /**
     * @param string $token
     */
    private function setCartToken(string $token)
    {
        $this->session->set(self::CART_TOKEN_KEY, $token);
    }

    #endregion
}
