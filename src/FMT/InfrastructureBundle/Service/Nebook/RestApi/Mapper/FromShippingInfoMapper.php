<?php

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\ShippingInfo;

class FromShippingInfoMapper extends AbstractMapper
{
    public function map(ShippingInfo $source) : array
    {
        $result = [
            "Address" => $this->mapper->map($source->getAddress(), "array"),
            "ShippingCodeId" => $source->getShippingCodeId(),
            "ShippingInstructions" => $source->getInstructions(),
        ];

        return $result;
    }
}
