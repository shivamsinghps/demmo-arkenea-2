<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Mapper;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\AbstractCustomer;

/**
 * Class AbstractCustomerMapper
 */
abstract class AbstractCustomerMapper extends AbstractMapper
{
    /**
     * @param AbstractCustomer $source
     *
     * @return array
     */
    protected function mapFromAbstractCustomerToArray(AbstractCustomer $source): array
    {
        $result = [
            'firstName' => $source->getFirstName(),
            'lastName' => $source->getLastName(),
            'email' => $source->getEmail(),
        ];

        if (!is_null($source->getIpAddress())) {
            $result['ipAddress'] = $source->getIpAddress();
        }

        if (!is_null($source->getCorrelationId())) {
            $result['correlationId'] = $source->getCorrelationId();
        }

        return $result;
    }

    /**
     * @param AbstractCustomer $customer
     * @param array            $source
     *
     * @return AbstractCustomer
     */
    protected function fillAbstractCustomerFromArray(AbstractCustomer $customer, array $source): AbstractCustomer
    {
        $customer
            ->setId($source['id'])
            ->setIri($source['_links']['self']['href'])
            ->setFirstName($source['firstName'])
            ->setLastName($source['lastName'])
            ->setEmail($source['email'])
            ->setIpAddress($source['ipAddress'] ?? null)
            ->setCorrelationId($source['correlationId'] ?? null)
        ;

        return $customer;
    }
}
