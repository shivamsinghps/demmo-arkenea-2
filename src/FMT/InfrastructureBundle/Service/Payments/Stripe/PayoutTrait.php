<?php

namespace FMT\InfrastructureBundle\Service\Payments\Stripe;

use Stripe\ApiResource;
use Stripe\Collection;
use Stripe\Payout;
use Stripe\StripeObject;

/**
 * Trait PayoutTrait
 * @package FMT\InfrastructureBundle\Service\Payments\Stripe
 */
trait PayoutTrait
{
    /**
     * @param array $params
     * @return Payout|ApiResource
     */
    public function createPayout(array $params): Payout
    {
        return Payout::create($params);
    }

    /**
     * @param string $id
     * @return Payout|StripeObject
     */
    public function getPayout(string $id): Payout
    {
        return Payout::retrieve($id);
    }

    /**
     * @param Payout $payout
     * @param array $params
     * @return Payout
     */
    public function updatePayout(Payout $payout, array $params): Payout
    {
        return $this->updateResource($payout, $params);
    }

    /**
     * @param array $params
     * @return Collection
     */
    public function getAllPayouts(array $params = [])
    {
        return Payout::all($params);
    }
}
