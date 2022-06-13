<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Mapper;

use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\ListInformation;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\WebhookSubscription;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\WebhookSubscriptionList;
use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;

/**
 * Class WebhookSubscriptionListMapper
 */
class WebhookSubscriptionListMapper extends AbstractMapper
{
    /**
     * @param array $source
     *
     * @return WebhookSubscriptionList
     */
    public function map(array $source): WebhookSubscriptionList
    {
        return new WebhookSubscriptionList(
            $this->mapper->mapList($source['_embedded']['webhook-subscriptions'], WebhookSubscription::class),
            $this->mapper->map($source, ListInformation::class)
        );
    }
}
