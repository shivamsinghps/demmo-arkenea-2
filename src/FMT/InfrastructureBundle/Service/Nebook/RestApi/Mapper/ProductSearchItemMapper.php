<?php
/**
 * Author: Anton Orlov
 * Date: 01.03.2018
 * Time: 11:09
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\ProductSearchItem;

class ProductSearchItemMapper extends AbstractMapper
{
    public function map(array $source) : ProductSearchItem
    {
        $result = new ProductSearchItem();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->id = $sourceWrapper->getString("Id");
        $resultWrapper->type = $sourceWrapper->getInt("TypeId");
        $resultWrapper->name = $sourceWrapper->getString("Name");
        $resultWrapper->description = $sourceWrapper->getString("Description");
        $resultWrapper->imageThumbnail = $sourceWrapper->getString("ImageThumbnail");
        $resultWrapper->quantity = $sourceWrapper->getInt("LocalInventory");
        $resultWrapper->minPrice = $this->toIntPrice($sourceWrapper->getString("MinPrice"));
        $resultWrapper->maxPrice = $this->toIntPrice($sourceWrapper->getString("MaxPrice"));
        $resultWrapper->catalogId = $sourceWrapper->getInt("FirstCatalog.Id");
        $resultWrapper->catalogName = $sourceWrapper->getString("FirstCatalog.Name");

        return $result;
    }
}
