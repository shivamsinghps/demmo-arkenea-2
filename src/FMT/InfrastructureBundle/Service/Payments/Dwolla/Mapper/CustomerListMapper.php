<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Mapper;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Exception\Exception;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\CustomerFactory;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\CustomerList;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\ListInformation;

/**
 * Class CustomerListMapper
 */
class CustomerListMapper extends AbstractMapper
{
    /**
     * @param array $source
     *
     * @return CustomerList
     * @throws Exception
     */
    public function map(array $source): CustomerList
    {
        $customers = [];

        foreach ($source['_embedded']['customers'] as $customer) {
            $customerClass = CustomerFactory::getClass($customer['type'] ?? null, $customer['businessType'] ?? null);
            $customers[] = $this->mapper->map($customer, $customerClass);
        }

        return new CustomerList($customers, $this->mapper->map($source, ListInformation::class));
    }
}
