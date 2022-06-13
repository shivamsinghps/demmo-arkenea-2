<?php

namespace FMT\DomainBundle\Service\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Common\Persistence\ManagerRegistry;
use FMT\DataBundle\Entity\CampaignBook;
use FMT\DataBundle\Entity\CampaignProductInterface;
use FMT\DataBundle\Entity\Order;
use FMT\DataBundle\Entity\OrderItem;
use FMT\DomainBundle\Event\CartActionEvent;
use FMT\DomainBundle\Exception\CartActionException;
use FMT\DomainBundle\Exception\CartConfigurationException;
use FMT\DomainBundle\Repository\OrderItemRepositoryInterface;
use FMT\DomainBundle\Repository\OrderRepositoryInterface;
use FMT\DomainBundle\Service\CartManagerInterface;
use FMT\DomainBundle\Service\CartProviderInterface;
use FMT\DomainBundle\Service\PaymentManagerInterface;
use FMT\DomainBundle\Service\BookManagerInterface;
use FMT\DomainBundle\Type\Cart\Summary;
use FMT\DomainBundle\Type\Payment\Donation;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class CartManager
 * @package FMT\DomainBundle\Service\Manager
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CartManager extends EventBasedManager implements CartManagerInterface
{
    const DUMMY_CART_PROVIDER_ALIAS = 'dummy';

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var CartProviderInterface[]|array
     */
    private $providers = [];

    /**
     * @var Order|null
     */
    private $cart = null;

    /**
     * @var PaymentManagerInterface
     */
    private $paymentManager;

    /**
     * @var BookManagerInterface
     */
    private $bookManager;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * CartManager constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(TokenStorageInterface $tokenStorage, OrderRepositoryInterface $orderRepository)
    {
        $this->tokenStorage = $tokenStorage;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param PaymentManagerInterface $paymentManager
     * @required
     */
    public function setPaymentManager(PaymentManagerInterface $paymentManager)
    {
        $this->paymentManager = $paymentManager;
    }

    /**
     * @param BookManagerInterface $bookManager
     * @required
     */
    public function setBookManager(BookManagerInterface $bookManager)
    {
        $this->bookManager = $bookManager;
    }

    /**
     * @param ManagerRegistry $doctrine
     * @required
     */
    public function setEntityManager(ManagerRegistry $doctrine) {
        $this->entityManager = $doctrine->getManager();
    }

    /**
     * @return Order
     */
    public function get(): Order
    {
        return $this->cart;
    }

    /**
     * @param CampaignProductInterface $product
     * @return bool
     */
    public function hasProduct(CampaignProductInterface $product): bool
    {
        foreach ($this->cart->getItems() as $orderItem) {
            if ($orderItem->getSku() === $product->getSku()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param CampaignProductInterface $product
     * @return bool
     */
    public function canAddProduct(CampaignProductInterface $product): bool
    {
        $productIsAvailable = ($product->getStatus() === CampaignBook::STATUS_AVAILABLE);
        // we can add only products from the same campaign
        $sameCampaignOrNewOrder = is_null($this->cart->getCampaign())
            || $this->cart->getCampaign() === $product->getCampaign();

        $allowedToDonate = $product->getCampaign()->getAllowedDonateAmount();
        $isAvailableForAddToCart = $allowedToDonate >= $product->getPrice() ?: false;

        return !$this->hasProduct($product) && $productIsAvailable && $sameCampaignOrNewOrder && $isAvailableForAddToCart;
    }

    /**
     * @param CampaignProductInterface $product
     * @return Order
     * @throws CartActionException
     */
    public function addProduct(CampaignProductInterface $product): Order
    {
        if ($this->hasProduct($product)) {
            throw new CartActionException('Cart items must be unique');
        }

        if ($product->isAvailable()) {
            $cartItem = $this->createCartItem($product);

            $this->cart->addItem($cartItem);

            $product->setStatus(CampaignBook::STATUS_UNAVAILABLE);
            $this->entityManager->flush();

            $this->dispatch(CartActionEvent::ADD_PRODUCT, new CartActionEvent($this->cart));
        }

        return $this->cart;
    }

    /**
     * @param CampaignProductInterface $product
     * @return bool
     * @throws CartActionException
     */
    public function removeProduct(CampaignProductInterface $product): bool
    {
        if (!$this->hasProduct($product)) {
            throw new CartActionException(sprintf('There is no "%s" book in the cart', $product->getTitle()));
        }

        foreach ($this->cart->getItems() as $orderItem) {
            if ($orderItem->getSku() === $product->getSku()) {
                $orderItem->removeLogs();
                $this->cart->removeItem($orderItem);

                $product->setStatus(CampaignBook::STATUS_AVAILABLE);
                $this->entityManager->flush();

                $this->dispatch(CartActionEvent::REMOVE_PRODUCT, new CartActionEvent($this->cart));
                return true;
            }
        }

        throw new CartActionException(sprintf(
            'Item "%s" is in the cart but for some reason has not been deleted',
            $product->getTitle()
        ));
    }

    /**
     * @inheritdoc
     */
    public function delete()
    {
        $this->orderRepository->remove($this->cart);
        $this->orderRepository->save();
    }

    /**
     * @inheritdoc
     */
    public function save()
    {
        return $this->orderRepository->save($this->cart);
    }

    /**
     * @inheritdoc
     */
    public function estimate(array $products): Summary
    {
        if (!array_key_exists(self::DUMMY_CART_PROVIDER_ALIAS, $this->providers)) {
            throw new CartConfigurationException('Dummy provider is not specified');
        }

        $cart = $this->providers[self::DUMMY_CART_PROVIDER_ALIAS]->createCart($this->tokenStorage->getToken());

        foreach ($products as $product) {
                $cartItem = $this->createCartItem($product);
                $cart->addItem($cartItem);
        }

        $this->dispatch(CartActionEvent::ESTIMATE_CART, new CartActionEvent($cart));

        return $this->getSummary($cart);
    }

    /**
     * @inheritdoc
     */
    public function getSummary(Order $cart = null): Summary
    {
        $cart = $cart ?? $this->cart;

        $summary = (new Summary())
            ->setItemsCount($cart->getItems()->count())
            ->setSubtotal($cart->getPrice())
            ->setShipping($cart->getShipping())
            ->setTax($cart->getTax())
            ->setFmtFee($cart->getFmtFee())
            ->setTransactionFee($cart->getTransactionFee())
            ->setTotal($cart->getTotal());

        return $summary;
    }

    #region Internal functions

    /**
     * @internal
     *
     * @param CartProviderInterface $provider
     * @param string $alias
     * @throws CartConfigurationException
     */
    public function addProvider(CartProviderInterface $provider, string $alias)
    {
        if (array_key_exists($alias, $this->providers)) {
            throw new CartConfigurationException(sprintf('Multiple cart providers with alias "%s"', $alias));
        }

        $this->providers[$alias] = $provider;
    }

    /**
     * @internal
     *
     * @throws NonUniqueResultException
     * @throws CartConfigurationException
     */
    public function initCart()
    {
        $token = $this->tokenStorage->getToken();

        if (is_null($token)) {
            return null;
        }

        foreach ($this->providers as $provider) {
            if ($provider->supports($token)) {
                $this->cart = $provider->getCart($token);

                break;
            }
        }

        if (is_null($this->cart)) {
            $this->cart = $this->create();
        }
    }

    /**
     * @return Order
     * @throws CartConfigurationException
     */
    private function create(): Order
    {
        $token = $this->tokenStorage->getToken();
        foreach ($this->providers as $provider) {
            if ($provider->supports($token)) {
                return $provider->createCart($token);
            }
        }

        throw new CartConfigurationException('No provider specified for a user');
    }

    /**
     * @param CampaignProductInterface|CampaignBook $product
     * @return OrderItem
     */
    private function createCartItem(CampaignProductInterface $product)
    {
        $cartItem = new OrderItem();
        $cartItem->setBook($product);
        $cartItem->setQuantity(1);
        $cartItem->setPrice($product->getPrice());
        $cartItem->setSku($product->getSku());
        $cartItem->setTitle($product->getTitle());
        $cartItem->setStatus(OrderItem::STATUS_CART);

        $cartItem->setOrder($this->cart);

        return $cartItem;
    }

    #endregion
    
    /**
     * @param Donation $donation
     * @param Order $order
     * @return array
     */
    public function sendDonationOrder(Donation $donation, Order $order)
    {
        $transaction = null;
        $orderExternalId = $this->sendOrder($order);
        if ($orderExternalId) {
            $transaction = $this->paymentManager->sendPaymentForOrder($donation, $order);
        }
        return [
            "orderExternalId" => $orderExternalId,
            "transaction" => $transaction
        ];
    }

    /**
     * @param Order $order
     * @return mixed|string
     * @throws \Exception
     */
    public function sendOrder(Order $order)
    {
        $orderExternalId = $this->bookManager->pushOrder($order);

        if ($orderExternalId) {
            $order->setStatus(Order::STATUS_COMPLETED);
            $order->setExternalId($orderExternalId);
            $order->setUnprocessedAmount($order->getPrice() + $order->getShipping());

            foreach ($order->getItems() as $orderItem) {
                $orderItem->setStatus(OrderItem::STATUS_SUBMITTED);
                $orderItem->getBook()->setStatus(CampaignBook::STATUS_ORDERED);
            }
            $this->orderRepository->save($order);
        }

        return $orderExternalId;
    }

    /**
     * @param Order $order
     * @return Order
     */
    public function saveOrder(Order $order)
    {
        $this->orderRepository->save($order);
        return $order;
    }
}
