<?php

namespace FMT\DataBundle\Mapper;

use FMT\DataBundle\Model\ShippingOption;
use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\ShippingCode as NebookShippingCode;

class ShippingOptionMapper
{
    public static function map(NebookShippingCode $source) : ShippingOption
    {
        $result = new ShippingOption();
        $resultWrapper = new DataHelper($result);

        $resultWrapper->id = $source->getId();
        $resultWrapper->name = $source->getName();

        return $result;
    }
}
