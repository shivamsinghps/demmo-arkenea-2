<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Api;

use DwollaSwagger\ApiException;
use DwollaSwagger\FundingsourcesApi;
use FMT\InfrastructureBundle\Service\Mapper\Mapper;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\FundingSource;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\FundingSourceList;

/**
 * Trait FundingSourcesTrait
 */
trait FundingSourcesTrait
{
    /**
     * @return Mapper
     */
    protected abstract function getMapper(): Mapper;

    /**
     * @var FundingsourcesApi|null
     */
    private $fundingSourcesApi;

    /**
     * @param FundingSource $fundingSource
     * @param string        $customerIri
     *
     * @return string Funding Source IRI
     */
    public function createCustomerFundingSource(FundingSource $fundingSource, string $customerIri): string
    {
        $body = $this->getMapper()->map($fundingSource, 'array');

        return (string) $this->getFundingSourcesApi()->createCustomerFundingSource($body, $customerIri);
    }

    /**
     * @param string $customerIri Customer IRI
     * @param bool   $removed
     *
     * @return FundingSourceList
     */
    public function getCustomerFundingSources(string $customerIri, bool $removed = false): FundingSourceList
    {
        try {
            $result = $this->getFundingSourcesApi()->getCustomerFundingSources($customerIri, $removed);

            return $this->getMapper()->map(json_decode(json_encode($result), true), FundingSourceList::class);
        } catch (ApiException $e) {
            if ($e->getCode() === 404) {
                return new FundingSourceList();
            }

            throw $e;
        }
    }

    /**
     * @return FundingsourcesApi
     */
    protected function getFundingSourcesApi(): FundingsourcesApi
    {
        if (is_null($this->fundingSourcesApi)) {
            $this->fundingSourcesApi = new FundingsourcesApi();
        }

        return $this->fundingSourcesApi;
    }
}
