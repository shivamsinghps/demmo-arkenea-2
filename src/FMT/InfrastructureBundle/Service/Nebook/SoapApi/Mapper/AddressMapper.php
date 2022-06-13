<?php
/**
 * Author: Vladimir Bykovsky
 * Date: 20.12.2021
 * Time: 15:10
 */

namespace FMT\InfrastructureBundle\Service\Nebook\SoapApi\Mapper;

use FMT\InfrastructureBundle\Service\Nebook\AbstractMapper;
use FMT\InfrastructureBundle\Service\Nebook\SoapApi\Item\Address;

/**
 * AddressMapper
 */
class AddressMapper extends AbstractMapper
{    
    /**
     * @param  Address $source
     * @return array
     */
    public function map(Address $source): array
    {
        return [
            "ShipName" => $source->getName(),
            "ShipAddress1" => $source->getAddress1(),
            "ShipAddress2" => $source->getAddress2(),
            "ShipCountry" => $source->getCountry(),
            "ShipCity" => $source->getCity(),
            "ShipState" => $source->getState(),
            "ShipZip" => $source->getZip(),
            "ShipPhone" => $source->getPhone(),
            "ShipEmail" => $source->getEmail()
        ];
    }
}
