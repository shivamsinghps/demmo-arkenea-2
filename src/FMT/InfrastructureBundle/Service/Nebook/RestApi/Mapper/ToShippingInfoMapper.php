<?php
/**
 * Author: Anton Orlov
 * Date: 05.03.2018
 * Time: 13:49
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Address;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\ShippingInfo;

class ToShippingInfoMapper extends AbstractMapper
{
    public function map(array $source) : ShippingInfo
    {
        $result = new ShippingInfo();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->shippingCodeId = $sourceWrapper->getInt("ShippingCode.Id");
        $resultWrapper->backofficeId = $sourceWrapper->getString("ShippingCode.BackofficeId");
        $resultWrapper->shippingName = $sourceWrapper->getString("ShippingCode.Name");
        $resultWrapper->instructions = $sourceWrapper->getString("ShippingInstructions");
        $resultWrapper->address = $this->mapper->map($sourceWrapper->get("ShippingAddress"), Address::class);

        return $result;
    }
}
