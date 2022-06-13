<?php
/**
 * Author: Vladimir Bykovsky
 * Date: 25.01.2022
 * Time: 15:10
 */

namespace FMT\DomainBundle\Service\Mapper\Order;

use FMT\DataBundle\Entity\UserProfile;
use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Nebook\SoapApi\Item\Address;

/**
 * AddressMapper
 */
class AddressMapper
{    
    /**
     * @param UserProfile $source
     * @return Address
     */
    public static function map(UserProfile $source) : Address
    {
        $result = new Address();
        $resultWrapper = new DataHelper($result);

        $resultWrapper->name = '';
        $resultWrapper->country = $source->getAddress()->getCountry();
        $resultWrapper->city = $source->getAddress()->getCity();
        $resultWrapper->state = $source->getAddress()->getRegion();
        $resultWrapper->zip = $source->getAddress()->getCode();
        $resultWrapper->address1 = $source->getAddress()->getAddress1();
        $resultWrapper->address2 = $source->getAddress()->getAddress2() ?? '';
        $resultWrapper->phone = '';
        $resultWrapper->email = $source->getEmail();

        return $result;
    }
}
