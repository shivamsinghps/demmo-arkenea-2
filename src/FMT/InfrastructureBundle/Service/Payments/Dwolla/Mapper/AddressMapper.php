<?php

declare(strict_types=1);

namespace FMT\InfrastructureBundle\Service\Payments\Dwolla\Mapper;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Payments\Dwolla\Item\Address;

/**
 * Class AddressMapper
 */
class AddressMapper extends AbstractMapper
{
    /**
     * @param Address $source
     *
     * @return array
     */
    public function mapToArray(Address $source): array
    {
        $result = [
            'address1' => $source->getAddress1(),
            'city' => $source->getCity(),
            'stateProvinceRegion' => $source->getStateProvinceRegion(),
            'country' => $source->getCountry(),
        ];

        if (!is_null($source->getAddress2())) {
            $result['address2'] = $source->getAddress2();
        }

        if (!is_null($source->getAddress3())) {
            $result['address3'] = $source->getAddress3();
        }

        if (!is_null($source->getPostalCode())) {
            $result['postalCode'] = $source->getPostalCode();
        }

        return $result;
    }

    /**
     * @param array $source
     *
     * @return Address
     */
    public function mapFromArray(array $source): Address
    {
        $result = new Address();
        $result
            ->setAddress1($source['address1'])
            ->setAddress2($source['address2'] ?? null)
            ->setAddress3($source['address3'] ?? null)
            ->setCity($source['city'])
            ->setStateProvinceRegion($source['stateProvinceRegion'])
            ->setPostalCode($source['postalCode'] ?? null)
            ->setCountry($source['country'])
        ;

        return $result;
    }
}
