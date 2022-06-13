<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla;

use DwollaSwagger\Configuration;
use FMT\InfrastructureBundle\Service\Mapper\Mapper;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Api\CustomersTrait;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Api\FundingSourcesTrait;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Api\TransfersTrait;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Api\WebhookSubscriptionsTrait;

/**
 * Class Client
 */
class Client
{
    use TransfersTrait;
    use CustomersTrait;
    use FundingSourcesTrait;
    use WebhookSubscriptionsTrait;

    /**
     * @var Mapper
     */
    protected $mapper;
    
    /**
     * @param OptionsInterface $options
     * @param Mapper $mapper
     */
    public function __construct(OptionsInterface $options, Mapper $mapper)
    {
        Configuration::$username = $options->getClientId();
        Configuration::$password = $options->getClientKey();
        Configuration::$apiClient = new ApiClient($options->getEndpoint());
        $this->mapper = $mapper;
    }

    /**
     * @inheritDoc
     */
    protected function getMapper(): Mapper
    {
        return $this->mapper;
    }

    /**
     * @return ApiClient
     */
    protected function getApiClient(): ApiClient
    {
        return Configuration::$apiClient;
    }
}
