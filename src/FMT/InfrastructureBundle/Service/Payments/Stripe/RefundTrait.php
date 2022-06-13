<?php

namespace FMT\InfrastructureBundle\Service\Payments\Stripe;

use Stripe\ApiResource;
use Stripe\Collection;
use Stripe\Refund;
use Stripe\StripeObject;

/**
 * Trait RefundTrait
 * @package FMT\InfrastructureBundle\Service\Payments\Stripe
 */
trait RefundTrait
{
    /**
     * @param array $params
     * @return Refund|ApiResource
     */
    public function createRefund(array $params): Refund
    {
        return Refund::create($params);
    }

    /**
     * @param string $id
     * @return Refund|StripeObject
     */
    public function getRefund(string $id): Refund
    {
        return Refund::retrieve($id);
    }

    /**
     * @param Refund $refund
     * @param array $params
     * @return Refund
     */
    public function updateRefund(Refund $refund, array $params): Refund
    {
        return $this->updateResource($refund, $params);
    }

    /**
     * @param array $params
     * @return Collection
     */
    public function getAllRefunds(array $params = [])
    {
        return Refund::all($params);
    }
}
