<?php

declare(strict_types=1);

namespace FMT\DomainBundle\Service\BookstorePayment;

use FMT\InfrastructureBundle\Service\Payments\Dwolla\Client;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\WebhookSubscription;

/**
 * Class WebhooksSubscriber
 */
class WebhooksSubscriber
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $secret;

    /**
     * @var string
     */
    protected $selfWebhookEndpoint;

    /**
     * @param Client $client
     * @param string $secret
     * @param string $selfWebhookEndpoint
     */
    public function __construct(Client $client, string $secret, string $selfWebhookEndpoint)
    {
        $this->client = $client;
        $this->secret = $secret;
        $this->selfWebhookEndpoint = $selfWebhookEndpoint;
    }

    public function subscribe(): void
    {
        if ($this->isSubscribed()) {
            return;
        }

        $webhookSubscription = new WebhookSubscription();
        $webhookSubscription
            ->setSecret($this->secret)
            ->setUrl($this->selfWebhookEndpoint)
        ;
        $this->client->createWebhookSubscription($webhookSubscription);
    }

    /**
     * @return bool
     */
    protected function isSubscribed(): bool
    {
        $list = $this->client->getWebhookSubscriptionList();

        foreach ($list->getWebhookSubscriptions() as $webhookSubscription) {
            if ($webhookSubscription->getUrl() !== $this->selfWebhookEndpoint) {
                continue;
            }

            if ($webhookSubscription->getPaused()) {
                $this->client->changeWebhookSubscription($webhookSubscription->getIri(), false);
            }

            return true;
        }

        return false;
    }
}
