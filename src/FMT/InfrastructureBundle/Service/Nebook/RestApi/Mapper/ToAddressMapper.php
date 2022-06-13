<?php
/**
 * Author: Anton Orlov
 * Date: 03.03.2018
 * Time: 16:49
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Address;

class ToAddressMapper extends AbstractMapper
{
    public function map(array $source) : Address
    {
        $result = new Address();
        $sourceWrapper = new DataHelper($source);

        $result->setCountry($sourceWrapper->getString("Country"));
        $result->setState($sourceWrapper->getString("State"));
        $result->setCity($sourceWrapper->getString("City"));
        $result->setZip($sourceWrapper->getString("Zip"));
        $result->setAddress1($sourceWrapper->getString("Address1"));
        $result->setAddress2($sourceWrapper->getString("Address2"));
        $result->setFirstName($sourceWrapper->getString("FirstName"));
        $result->setLastName($sourceWrapper->getString("LastName"));
        $result->setPhone($sourceWrapper->getString("Phone"));

        return $result;
    }
}
