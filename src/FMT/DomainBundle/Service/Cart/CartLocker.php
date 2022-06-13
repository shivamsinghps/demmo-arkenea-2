<?php

namespace FMT\DomainBundle\Service\Cart;

use FMT\DomainBundle\Service\ResourceLockerInterface;
use FMT\InfrastructureBundle\Service\AwsLambda\ExecutorInterface;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\Lock;

/**
 * Class CartService
 * @package FMT\DomainBundle\Service\Cart
 */
class CartLocker implements ResourceLockerInterface
{
    const CART_LOCK_RESOURCE_KEY = 'fmt.cart.lock';

    /**
     * @var Lock
     */
    private $lock = null;

    /**
     * @var Factory
     */
    private $lockFactory;

    /**
     * @var ExecutorInterface
     */
    private $awsExecutor;

    /**
     * CheckoutService constructor.
     * @param ExecutorInterface $awsExecutor
     * @param Factory $lockFactory
     */
    public function __construct(ExecutorInterface $awsExecutor, Factory $lockFactory)
    {
        $this->awsExecutor = $awsExecutor;
        $this->lockFactory = $lockFactory;
    }

    /**
     * @inheritdoc
     */
    public function lock()
    {
        if ($this->isLocked()) {
            return false;
        }

        $this->lock = $this->lockFactory->createLock(self::CART_LOCK_RESOURCE_KEY);

        return $this->lock->acquire();
    }

    public function release()
    {
        $this->lock->release();
        $this->lock = null;
    }

    /**
     * @inheritdoc
     */
    public function isLocked()
    {
        $lock = $this->lockFactory->createLock(self::CART_LOCK_RESOURCE_KEY);

        if ($lock->acquire()) {
            $lock->release();

            return false;
        }

        return true;
    }
}
