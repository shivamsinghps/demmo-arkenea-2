<?php

namespace FMT\InfrastructureBundle\Service\Payments\Stripe;

use Stripe\ApiResource;
use Stripe\Charge;
use Stripe\Collection;
use Stripe\StripeObject;

/**
 * Trait ChargeTrait
 * @package FMT\InfrastructureBundle\Service\Payments\Stripe
 */
trait ChargeTrait
{
    /**
     * @param array $params
     * @return Collection
     */
    public function getAllCharges(array $params = []): Collection
    {
        return Charge::all($params);
    }

    /**
     * @param array $params
     * @return Charge|ApiResource
     */
    public function createCharge(array $params): Charge
    {
        return Charge::create($params);
    }

    /**
     * @param string $id
     * @return Charge|StripeObject
     */
    public function getCharge(string $id): Charge
    {
        return Charge::retrieve($id);
    }

    /**
     * @param Charge $charge
     * @param array $params
     * @return Charge
     */
    public function updateCharge(Charge $charge, array $params): Charge
    {
        return $this->updateResource($charge, $params);
    }
}
