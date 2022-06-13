<?php
/**
 * Author: Anton Orlov
 * Date: 03.03.2018
 * Time: 16:51
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Address;

class FromAddressMapper extends AbstractMapper
{
    public function map(Address $source) : array
    {
        return [
            "Address1" => $source->getAddress1(),
            "Address2" => $source->getAddress2(),
            "City" => $source->getCity(),
            "Country" => $source->getCountry(),
            "Phone" => $source->getPhone(),
            "State" => $source->getState(),
            "Zip" => $source->getZip(),
            "FirstName" => $source->getFirstName(),
            "LastName" => $source->getLastName()
        ];
    }
}
