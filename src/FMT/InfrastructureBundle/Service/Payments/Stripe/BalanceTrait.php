<?php

namespace FMT\InfrastructureBundle\Service\Payments\Stripe;

use Stripe\Balance;
use Stripe\Exception\ApiErrorException;

/**
 * Trait BalanceTrait
 * @package FMT\InfrastructureBundle\Service\Payments\Stripe
 */
trait BalanceTrait
{
    /**
     * @return Balance
     * @throws ApiErrorException
     */
    public function getBalance(): Balance
    {
        return Balance::retrieve();
    }
}
