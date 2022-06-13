<?php

namespace FMT\DomainBundle\EventSubscriber\Cart;

use FMT\DomainBundle\Event\CartActionEvent;
use FMT\DomainBundle\Exception\CartConfigurationException;
use FMT\DomainBundle\Service\CartProcessorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class CartActionSubscriber
 * @package FMT\DomainBundle\EventSubscriber\Cart
 */
class CartActionSubscriber implements EventSubscriberInterface
{
    /**
     * @var CartProcessorInterface[]
     */
    private $processors = [];

    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            CartActionEvent::ADD_PRODUCT => [
                ['recalculateOrder']
            ],
            CartActionEvent::REMOVE_PRODUCT => [
                ['recalculateOrder']
            ],
            CartActionEvent::ESTIMATE_CART => [
                ['recalculateOrder']
            ],
        ];
    }

    /**
     * @param CartActionEvent $event
     */
    public function recalculateOrder(CartActionEvent $event)
    {
        foreach ($this->processors as $processor) {
            $processor->process($event->getCart());
        }
    }

    #region Internal

    /**
     * @internal
     *
     * @param CartProcessorInterface $processor
     * @param string $alias
     * @throws CartConfigurationException
     */
    public function addProcessor(CartProcessorInterface $processor, string $alias)
    {
        if (array_key_exists($alias, $this->processors)) {
            throw new CartConfigurationException(sprintf('Multiple cart processors with alias "%s"', $alias));
        }

        $this->processors[] = $processor;
    }

    #endregion
}
