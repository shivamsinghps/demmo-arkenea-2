<?php
/**
 * Author: Anton Orlov
 * Date: 28.02.2018
 * Time: 17:24
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Material;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\ProductFamily;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Requirement;

class MaterialMapper extends AbstractMapper
{
    public function map(array $source) : Material
    {
        $result = new Material();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->backofficeId = $sourceWrapper->getString("BackofficeId");
        $resultWrapper->actualBuyback = $sourceWrapper->getInt("ActualBuyback");
        $resultWrapper->estimatedBuyback = $sourceWrapper->getInt("EstimatedBuyback");
        $resultWrapper->isNewOnly = $sourceWrapper->getBool("IsNewOnly");
        $resultWrapper->isRentOnly = $sourceWrapper->getBool("IsRentOnly");
        $resultWrapper->quantity = $sourceWrapper->getInt("QuantityToProvide");
        $resultWrapper->shortOrder = $sourceWrapper->getInt("ShortOrder");

        if ($requirement = $sourceWrapper->get("Requirement")) {
            $resultWrapper->requirement = $this->mapper->map($requirement, Requirement::class);
        }

        if ($family = $sourceWrapper->get("ProductFamily")) {
            $resultWrapper->family = $this->mapper->map($family, ProductFamily::class);
        }

        return $result;
    }
}
