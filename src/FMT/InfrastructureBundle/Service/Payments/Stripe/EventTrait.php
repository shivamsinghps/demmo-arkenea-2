<?php

namespace FMT\InfrastructureBundle\Service\Payments\Stripe;

use Stripe\Collection;
use Stripe\Event;
use Stripe\StripeObject;

/**
 * Trait EventTrait
 * @package FMT\InfrastructureBundle\Service\Payments\Stripe
 */
trait EventTrait
{
    /**
     * @param string $id
     * @return Event|StripeObject
     */
    public function getEvent(string $id): Event
    {
        return Event::retrieve($id);
    }

    /**
     * @param array $params
     * @return Collection
     */
    public function getAllEvents(array $params = [])
    {
        return Event::all($params);
    }
}
