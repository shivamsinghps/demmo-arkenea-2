<?php
/**
 * Author: Vladimir Bykovsky
 * Date: 11.11.2021
 * Time: 15:10
 */

namespace FMT\InfrastructureBundle\Service\Nebook\SoapApi\Mapper;

use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Nebook\AbstractMapper;
use FMT\InfrastructureBundle\Service\Nebook\SoapApi\Item\ShippingCode;

/**
 * ShippingCodeMapper
 */
class ShippingCodeMapper extends AbstractMapper
{    
    /**
     * @param  array $source
     * @return ShippingCode
     */
    public function map(array $source): ShippingCode
    {
        $result = new ShippingCode();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->id = $sourceWrapper->getInt("ShipCodeID");
        $resultWrapper->backofficeId = $sourceWrapper->getInt("BackofficeShipCodeID");
        $resultWrapper->type = $sourceWrapper->getString("ShipType");
        $resultWrapper->method = $sourceWrapper->getString("ShipMethod");

        return $result;
    }
}
