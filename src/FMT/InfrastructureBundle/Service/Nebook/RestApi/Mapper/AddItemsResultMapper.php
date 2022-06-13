<?php
/**
 * Author: Anton Orlov
 * Date: 05.03.2018
 * Time: 13:11
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\AddItemsResult;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\AddItemResult;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\CartSummary;

class AddItemsResultMapper extends AbstractMapper
{
    public function map(array $source) : AddItemsResult
    {
        $result = new AddItemsResult();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->results = $this->mapper->mapList(
            $sourceWrapper->get("AddedItemResults", []),
            AddItemResult::class
        );
        $resultWrapper->summary = $this->mapper->map($sourceWrapper->get("CartSummary"), CartSummary::class);

        return $result;
    }
}
