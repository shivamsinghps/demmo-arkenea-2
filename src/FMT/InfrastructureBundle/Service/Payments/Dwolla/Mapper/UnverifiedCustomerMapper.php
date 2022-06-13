<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Mapper;

use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\UnverifiedCustomer;

/**
 * Class UnverifiedCustomerMapper
 */
class UnverifiedCustomerMapper extends AbstractCustomerMapper
{
    /**
     * @param UnverifiedCustomer $source
     *
     * @return array
     */
    public function mapToArray(UnverifiedCustomer $source): array
    {
        $result = $this->mapFromAbstractCustomerToArray($source);

        if (!is_null($source->getBusinessName())) {
            $result['businessName'] = $source->getBusinessName();
        }

        return $result;
    }

    /**
     * @param array $source
     *
     * @return UnverifiedCustomer
     */
    public function mapFromArray(array $source): UnverifiedCustomer
    {
        /** @var UnverifiedCustomer $result */
        $result = $this->fillAbstractCustomerFromArray(new UnverifiedCustomer(), $source);
        $result->setBusinessName($source['businessName'] ?? null);

        return $result;
    }
}
