<?php
/**
 * Author: Vladimir Bykovsky
 * Date: 25.01.2022
 * Time: 15:10
 */

namespace FMT\DomainBundle\Service\Mapper\Order;

use FMT\DataBundle\Entity\Order;
use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Nebook\SoapApi\Item\Address;
use FMT\InfrastructureBundle\Service\Nebook\SoapApi\Item\Shipping;

/**
 * ShippingMapper
 */
class ShippingMapper
{    
    /**
     * @param Order $source
     * @return Shipping
     */
    public static function map(Order $source) : Shipping
    {
        $result = new Shipping();
        $resultWrapper = new DataHelper($result);

        $user = $source->getCampaign()->getUser();
        $resultWrapper->id = $source->getCampaign()->getShippingOption();
        $resultWrapper->amount = $source->getShipping() / 100;
        $resultWrapper->address = AddressMapper::map($user->getProfile(), Address::class);
        $resultWrapper->instr = '';

        return $result;
    }
}
