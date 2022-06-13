<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla;

/**
 * Class Options
 */
class Options implements OptionsInterface
{
    /**
     * @var string
     */
    private $endpoint;

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientKey;

    /**
     * @param string $endpoint
     * @param string $clientId
     * @param string $clientKey
     */
    public function __construct(string $endpoint, string $clientId, string $clientKey)
    {
        $this->endpoint = $endpoint;
        $this->clientId = $clientId;
        $this->clientKey = $clientKey;
    }

    /**
     * @inheritDoc
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * @inheritDoc
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @inheritDoc
     */
    public function getClientKey(): string
    {
        return $this->clientKey;
    }
}
