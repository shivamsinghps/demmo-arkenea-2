<?php

namespace FMT\InfrastructureBundle\Service\Payments\Stripe;

use FMT\InfrastructureBundle\Helper\CaseHelper;
use Stripe\ApiOperations\Update;
use Stripe\ApiResource;
use Stripe\Stripe;

/**
 * Class Stripe
 * @package FMT\InfrastructureBundle\Service\Payments
 */
class StripeClient
{
    use BalanceTrait,
        BalanceTransactionTrait,
        ChargeTrait,
        CustomerTrait,
        EventTrait,
        InvoiceTrait,
        PayoutTrait,
        RefundTrait;

    /**
     * Stripe constructor.
     * @param string $secretKey
     */
    public function __construct(string $secretKey)
    {
        Stripe::setApiKey($secretKey);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     *
     * @param ApiResource|Update $resource
     * @param array $params
     * @return mixed
     */
    private function updateResource(ApiResource $resource, array $params): ApiResource
    {
        foreach ($params as $name => $value) {
            $resource->{CaseHelper::toCase($name, CaseHelper::SNAKE_CASE, CaseHelper::CAMEL_CASE)} = $value;
        }

        return $resource->save();
    }
}
