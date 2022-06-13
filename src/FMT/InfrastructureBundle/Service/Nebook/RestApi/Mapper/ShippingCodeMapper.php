<?php
/**
 * Author: Anton Orlov
 * Date: 05.03.2018
 * Time: 13:49
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\ShippingCode;

class ShippingCodeMapper extends AbstractMapper
{
    public function map(array $source) : ShippingCode
    {
        $result = new ShippingCode();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->id = $sourceWrapper->getInt("Id");
        $resultWrapper->name = $sourceWrapper->getString("Name");
        $resultWrapper->description = $sourceWrapper->getString("Description");
        $resultWrapper->backofficeId = $sourceWrapper->getString("BackofficeId");
        $resultWrapper->calculationType = $sourceWrapper->getInt("CalculationType");
        $resultWrapper->sortOrder = $sourceWrapper->getInt("SortOrder");
        $resultWrapper->details = $sourceWrapper->get("Details", []);

        return $result;
    }
}
