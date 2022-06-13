<?php

namespace FMT\DomainBundle\Service\Cart;

use FMT\DomainBundle\Event\CartCheckoutEvent;
use FMT\InfrastructureBundle\Service\AwsLambda\ExecutorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class CheckoutService
 * @package FMT\DomainBundle\Service\Cart
 */
class CheckoutService
{
    const CART_CHECKOUT_LAMBDA_FUNCTION = 'submitCart';

    /**
     * The logger instance.
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ExecutorInterface
     */
    private $lambdaExecutor;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * CheckoutService constructor.
     * @param ExecutorInterface $lambdaExecutor
     * @param LoggerInterface $logger
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        ExecutorInterface $lambdaExecutor,
        LoggerInterface $logger,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->lambdaExecutor = $lambdaExecutor;
        $this->logger = $logger;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param string $shopperId
     * @return bool
     */
    public function checkout(string $shopperId)
    {
        try {
            $result = $this->lambdaExecutor->invoke(self::CART_CHECKOUT_LAMBDA_FUNCTION, ['shopperId' => $shopperId]);

            if (empty($result['error'])) {
                $this->logger->info($this->logMessage($shopperId, $result));
                $this->eventDispatcher->dispatch(
                    CartCheckoutEvent::CHECKOUT_COMPLETED,
                    new CartCheckoutEvent($shopperId, $result)
                );

                return true;
            } else {
                $this->logger->error($this->logMessage($shopperId, $result));
                $this->eventDispatcher->dispatch(
                    CartCheckoutEvent::CHECKOUT_COMPLETED,
                    new CartCheckoutEvent($shopperId, $result)
                );
            }
        } catch (\Exception $e) {
            $this->logger->error($this->logMessage($shopperId, $e->getMessage()), $e->getTrace());
            $this->eventDispatcher->dispatch(
                CartCheckoutEvent::CHECKOUT_COMPLETED,
                new CartCheckoutEvent($shopperId, [$e->getMessage()])
            );
        }

        return false;
    }

    /**
     * @param string $shopperId
     * @param $response
     * @return string
     */
    private function logMessage(string $shopperId, $response)
    {
        return sprintf('Cart Checkout (Shopper ID#%s): %s', $shopperId, json_encode($response));
    }
}
