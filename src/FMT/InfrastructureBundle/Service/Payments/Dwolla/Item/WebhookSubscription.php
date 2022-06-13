<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Item;

use DateTime;

/**
 * Class WebhookSubscription
 */
class WebhookSubscription
{
    /**
     * @var string|null
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $iri;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $secret;

    /**
     * @var DateTime|null
     */
    protected $created;

    /**
     * @var bool|null
     */
    protected $paused;

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     *
     * @return WebhookSubscription
     */
    public function setId(?string $id): WebhookSubscription
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIri(): ?string
    {
        return $this->iri;
    }

    /**
     * @param string|null $iri
     *
     * @return WebhookSubscription
     */
    public function setIri(?string $iri): WebhookSubscription
    {
        $this->iri = $iri;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return WebhookSubscription
     */
    public function setUrl(string $url): WebhookSubscription
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     *
     * @return WebhookSubscription
     */
    public function setSecret(string $secret): WebhookSubscription
    {
        $this->secret = $secret;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getCreated(): ?DateTime
    {
        return $this->created;
    }

    /**
     * @param DateTime|null $created
     *
     * @return WebhookSubscription
     */
    public function setCreated(?DateTime $created): WebhookSubscription
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getPaused(): ?bool
    {
        return $this->paused;
    }

    /**
     * @param bool|null $paused
     *
     * @return WebhookSubscription
     */
    public function setPaused(?bool $paused): WebhookSubscription
    {
        $this->paused = $paused;

        return $this;
    }
}
