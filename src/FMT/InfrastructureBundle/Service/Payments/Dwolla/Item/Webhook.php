<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Item;

use DateTime;

/**
 * Class Webhook
 */
class Webhook
{
    /**
     * @var string|null IRI
     */
    protected $self;

    /**
     * @var string|null IRI
     */
    protected $account;

    /**
     * @var string|null IRI
     */
    protected $resource;

    /**
     * @var string|null IRI
     */
    protected $customer;

    /**
     * @var DateTime
     */
    protected $created;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $resourceId;

    /**
     * @var string
     */
    protected $topic;

    /**
     * @return string|null
     */
    public function getSelf(): ?string
    {
        return $this->self;
    }

    /**
     * @param string|null $self
     *
     * @return Webhook
     */
    public function setSelf(?string $self): Webhook
    {
        $this->self = $self;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAccount(): ?string
    {
        return $this->account;
    }

    /**
     * @param string|null $account
     *
     * @return Webhook
     */
    public function setAccount(?string $account): Webhook
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getResource(): ?string
    {
        return $this->resource;
    }

    /**
     * @param string|null $resource
     *
     * @return Webhook
     */
    public function setResource(?string $resource): Webhook
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCustomer(): ?string
    {
        return $this->customer;
    }

    /**
     * @param string|null $customer
     *
     * @return Webhook
     */
    public function setCustomer(?string $customer): Webhook
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreated(): DateTime
    {
        return $this->created;
    }

    /**
     * @param DateTime $created
     *
     * @return Webhook
     */
    public function setCreated(DateTime $created): Webhook
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return Webhook
     */
    public function setId(string $id): Webhook
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return $this->resourceId;
    }

    /**
     * @param string $resourceId
     *
     * @return Webhook
     */
    public function setResourceId(string $resourceId): Webhook
    {
        $this->resourceId = $resourceId;

        return $this;
    }

    /**
     * @return string
     */
    public function getTopic(): string
    {
        return $this->topic;
    }

    /**
     * @param string $topic
     *
     * @return Webhook
     */
    public function setTopic(string $topic): Webhook
    {
        $this->topic = $topic;

        return $this;
    }
}
