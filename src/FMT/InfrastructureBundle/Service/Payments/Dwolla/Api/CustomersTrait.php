<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Api;

use DwollaSwagger\CustomersApi;
use FMT\InfrastructureBundle\Service\Mapper\Mapper;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\ApiClient;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\AbstractCustomer;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\BeneficialOwner;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\CustomerList;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\CustomerListFilter;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\ListInformation;

/**
 * Trait CustomersTrait
 */
trait CustomersTrait
{
    /**
     * @return Mapper
     */
    protected abstract function getMapper(): Mapper;

    /**
     * @return ApiClient
     */
    protected abstract function getApiClient(): ApiClient;

    /**
     * @var CustomersApi|null
     */
    private $customersApi;

    /**
     * @param AbstractCustomer $user
     *
     * @return string Customer IRI
     */
    public function createCustomer(AbstractCustomer $user): string
    {
        $body = $this->getMapper()->map($user, 'array');

        return (string) $this->getCustomersApi()->create($body);
    }

    /**
     * @param BeneficialOwner $beneficialOwner
     * @param string          $verifiedCustomerIri
     *
     * @return string Beneficial Owner IRI
     */
    public function addBeneficialOwner(BeneficialOwner $beneficialOwner, string $verifiedCustomerIri): string
    {
        $body = $this->getMapper()->map($beneficialOwner, 'array');

        return (string) $this->getCustomersApi()->addBeneficialOwner($body, $verifiedCustomerIri);
    }

    /**
     * @param int                     $limit
     * @param int                     $offset
     * @param CustomerListFilter|null $filter
     *
     * @return CustomerList
     */
    public function getCustomerList(
        int $limit,
        int $offset = 0,
        ?CustomerListFilter $filter = null
    ): CustomerList {
        if (is_null($filter)) {
            $result = $this->getCustomersApi()->_list($limit, $offset);
        } else {
            $result = $this->getCustomersApi()->_list(
                $limit,
                $offset,
                $filter->getSearch(),
                $filter->getStatus(),
                null,
                $filter->getEmail()
            );
        }

        return $this->getMapper()->map(json_decode(json_encode($result), true), CustomerList::class);
    }

    /**
     * @param CustomerListFilter|null $filter
     *
     * @return int
     */
    public function getTotalCustomers(?CustomerListFilter $filter = null): int
    {
        $api = $this->getCustomersApi();

        if (is_null($filter)) {
            $result = $api->_list(1, 0);
        } else {
            $result = $api->_list(1, 0, $filter->getSearch(), $filter->getStatus(), null, $filter->getEmail());
        }

        $array = json_decode(json_encode($result), true);
        /** @var ListInformation $listInformation */
        $listInformation = $this->getMapper()->map($array, ListInformation::class);

        return $listInformation->getTotal();
    }

    /**
     * @return CustomersApi
     */
    protected function getCustomersApi(): CustomersApi
    {
        if (is_null($this->customersApi)) {
            $this->customersApi = new CustomersApi();
        }

        return $this->customersApi;
    }
}
