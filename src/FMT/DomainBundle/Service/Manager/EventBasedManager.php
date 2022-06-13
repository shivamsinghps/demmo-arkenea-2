<?php
/**
 * Author: Anton Orlov
 * Date: 23.03.2018
 * Time: 10:33
 */

namespace FMT\DomainBundle\Service\Manager;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class EventBasedManager
 * @package FMT\DomainBundle\Service
 */
abstract class EventBasedManager
{
    /** @var EventDispatcherInterface */
    private $dispatcher;

    /**
     * @required
     * @param EventDispatcherInterface $dispatcher
     * @required
     */
    public function setEventDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param $name
     * @param Event $event
     * @return Event
     */
    protected function dispatch($name, Event $event)
    {
        return $this->dispatcher->dispatch($name, $event);
    }
}
