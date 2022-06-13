<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Item;

/**
 * Class WebhookSubscriptionList
 */
class WebhookSubscriptionList
{
    /**
     * @var WebhookSubscription[]
     */
    protected $webhookSubscriptions;

    /**
     * @var ListInformation|null
     */
    protected $information;

    /**
     * @param WebhookSubscription[] $webhookSubscriptions
     */
    public function __construct(array $webhookSubscriptions = [], ?ListInformation $information = null)
    {
        $this->webhookSubscriptions = $webhookSubscriptions;
        $this->information = $information;
    }

    /**
     * @return WebhookSubscription[]
     */
    public function getWebhookSubscriptions(): array
    {
        return $this->webhookSubscriptions;
    }

    /**
     * @param WebhookSubscription[] $webhookSubscriptions
     *
     * @return WebhookSubscriptionList
     */
    public function setWebhookSubscriptions(array $webhookSubscriptions): WebhookSubscriptionList
    {
        $this->webhookSubscriptions = $webhookSubscriptions;

        return $this;
    }

    /**
     * @return ListInformation|null
     */
    public function getInformation(): ?ListInformation
    {
        return $this->information;
    }

    /**
     * @param ListInformation|null $information
     *
     * @return WebhookSubscriptionList
     */
    public function setInformation(?ListInformation $information): WebhookSubscriptionList
    {
        $this->information = $information;

        return $this;
    }
}
