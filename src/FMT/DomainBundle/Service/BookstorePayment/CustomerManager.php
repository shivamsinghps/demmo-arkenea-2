<?php

declare(strict_types=1);

namespace FMT\DomainBundle\Service\BookstorePayment;

use FMT\InfrastructureBundle\Service\Payments\Dwolla\Client;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\CustomerListFilter;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\FundingSource;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\ReceiveOnlyUser;

/**
 * Class CustomerManager
 */
class CustomerManager
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var CustomerOptions
     */
    private $customerOptions;

    /**
     * @var FundingSourceOptions
     */
    private $fundingSourceOptions;

    /**
     * @var null
     */
    private $fundingSourceIri = null;

    /**
     * @param Client               $client
     * @param CustomerOptions      $customerOptions
     * @param FundingSourceOptions $fundingSourceOptions
     */
    public function __construct(
        Client $client,
        CustomerOptions $customerOptions,
        FundingSourceOptions $fundingSourceOptions
    ) {
        $this->client = $client;
        $this->customerOptions = $customerOptions;
        $this->fundingSourceOptions = $fundingSourceOptions;
    }

    /**
     * @return string
     */
    public function getFundingSourceIri(): string
    {
        if (is_null($this->fundingSourceIri)) {
            $this->fundingSourceIri = $this->getFundingSource();
        }

        return $this->fundingSourceIri;
    }

    /**
     * @return string|null
     */
    protected function getCustomer(): ?string
    {
        $filter = new CustomerListFilter();
        $filter->setEmail($this->customerOptions->getEmail());
        $list = $this->client->getCustomerList(1, 0, $filter);

        if (empty($list->getCustomers())) {
            return $this->createCustomer();
        }

        return $list->getCustomers()[0]->getIri();
    }

    /**
     * @return string|null
     */
    protected function getFundingSource(): ?string
    {
        $customerIri = $this->getCustomer();
        $list = $this->client->getCustomerFundingSources($customerIri);

        foreach ($list->getFundingSources() as $fundingSource) {
            if ($fundingSource->getName() === $this->fundingSourceOptions->getName()) {
                return $fundingSource->getIri();
            }
        }

        return $this->createFundingSource();
    }

    /**
     * @return string Customer IRI
     */
    protected function createCustomer(): string
    {
        $customer = new ReceiveOnlyUser();
        $customer
            ->setFirstName($this->customerOptions->getFirstName())
            ->setLastName($this->customerOptions->getLastName())
            ->setEmail($this->customerOptions->getEmail())
            ->setBusinessName($this->customerOptions->getBusinessName())
            ->setIpAddress($this->customerOptions->getIpAddress())
        ;

        return $this->client->createCustomer($customer);
    }

    /**
     * @return string Founding Source IRI
     */
    protected function createFundingSource(): string
    {
        $customerIri = $this->getCustomer();
        $fundingSource = new FundingSource();
        $fundingSource
            ->setName($this->fundingSourceOptions->getName())
            ->setRoutingNumber($this->fundingSourceOptions->getRoutingNumber())
            ->setAccountNumber($this->fundingSourceOptions->getAccountNumber())
            ->setBankAccountType(FundingSource::BANK_ACCOUNT_TYPE_CHECKING)
        ;

        return $this->client->createCustomerFundingSource($fundingSource, $customerIri);
    }
}
