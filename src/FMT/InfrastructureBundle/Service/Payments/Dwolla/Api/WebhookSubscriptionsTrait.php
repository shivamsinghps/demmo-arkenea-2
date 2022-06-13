<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Api;

use DwollaSwagger\WebhooksubscriptionsApi;
use FMT\InfrastructureBundle\Service\Mapper\Mapper;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\WebhookSubscription;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\WebhookSubscriptionList;

/**
 * Trait WebhookSubscriptionsTrait
 */
trait WebhookSubscriptionsTrait
{
    /**
     * @return Mapper
     */
    protected abstract function getMapper(): Mapper;

    /**
     * @var WebhooksubscriptionsApi|null
     */
    private $webhookSubscriptionsApi;

    /**
     * @param WebhookSubscription $webhookSubscription
     *
     * @return string Webhook Subscription IRI
     */
    public function createWebhookSubscription(WebhookSubscription $webhookSubscription): string
    {
        $body = $this->getMapper()->map($webhookSubscription, 'array');

        return (string) $this->getWebhookSubscriptionsApi()->create($body);
    }

    /**
     * @param string $webhookSubscriptionIri
     */
    public function deleteWebhookSubscription(string $webhookSubscriptionIri): void
    {
        $this->getWebhookSubscriptionsApi()->deleteById($webhookSubscriptionIri);
    }

    /**
     * @return WebhookSubscriptionList
     */
    public function getWebhookSubscriptionList(): WebhookSubscriptionList
    {
        $data = json_decode(json_encode($this->getWebhookSubscriptionsApi()->_list()), true);

        return $this->getMapper()->map($data, WebhookSubscriptionList::class);
    }

    /**
     * @param string $webhookSubscriptionIri
     * @param bool   $paused
     */
    public function changeWebhookSubscription(string $webhookSubscriptionIri, bool $paused): void
    {
        $this->getWebhookSubscriptionsApi()->updateSubscription(['paused' => $paused], $webhookSubscriptionIri);
    }

    /**
     * @return WebhooksubscriptionsApi
     */
    private function getWebhookSubscriptionsApi(): WebhooksubscriptionsApi
    {
        if (is_null($this->webhookSubscriptionsApi)) {
            $this->webhookSubscriptionsApi = new WebhooksubscriptionsApi();
        }

        return $this->webhookSubscriptionsApi;
    }
}
