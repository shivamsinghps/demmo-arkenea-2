<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Mapper;

use DateTime;
use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\Amount;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\CardDetails;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\FundingSource;

/**
 * Class FundingSourceMapper
 */
class FundingSourceMapper extends AbstractMapper
{
    /**
     * @param FundingSource $source
     *
     * @return array
     */
    public function mapToArray(FundingSource $source): array
    {
        $result = [
            'routingNumber' => $source->getRoutingNumber(),
            'accountNumber' => $source->getAccountNumber(),
            'bankAccountType' => $source->getBankAccountType(),
            'name' => $source->getName(),
        ];

        if (!is_null($source->getOnDemandAuthorization())) {
            $result['_links']['on-demand-authorization']['href'] = $source->getOnDemandAuthorization();
        }

        if (!is_null($source->getPlaidToken())) {
            $result['plaidToken'] = $source->getPlaidToken();
        }

        if (!is_null($source->getChannels())) {
            $result['channels'] = $source->getChannels();
        }

        return $result;
    }

    /**
     * @param array $source
     *
     * @return FundingSource
     */
    public function mapFromArray(array $source): FundingSource
    {
        $result = new FundingSource();
        $result->setId($source['id']);
        $result->setIri($source['_links']['self']['href']);
        $result->setStatus($source['status']);
        $result->setType($source['type']);
        $result->setBankAccountType($source['bankAccountType']);
        $result->setName($source['name']);
        $result->setCreated(new DateTime($source['created']));
        $result->setRemoved($source['removed']);
        $result->setChannels($source['channels']);
        $result->setBankName($source['bankName']);
        $result->setIavAccountHolders($source['iavAccountHolders'] ?? null);
        $result->setFingerprint($source['fingerprint'] ?? null);

        if (isset($source['balance'])) {
            $result->setBalance($this->mapper->map($source['balance'], Amount::class));
        }

        if (isset($source['cardDetails'])) {
            $result->setCardDetails($this->mapper->map($source['cardDetails'], CardDetails::class));
        }

        return $result;
    }
}
