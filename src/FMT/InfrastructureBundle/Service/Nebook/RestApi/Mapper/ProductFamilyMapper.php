<?php
/**
 * Author: Anton Orlov
 * Date: 28.02.2018
 * Time: 17:42
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\BookInfo;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Product;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\ProductFamily;

class ProductFamilyMapper extends AbstractMapper
{
    public function map(array $source) : ProductFamily
    {
        $result = new ProductFamily();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->id = $sourceWrapper->getString("Id");
        $resultWrapper->backofficeId = $sourceWrapper->getString("BackofficeId");
        $resultWrapper->type = $sourceWrapper->getInt("Type");
        $resultWrapper->name = $sourceWrapper->getString("Name");
        $resultWrapper->description = $sourceWrapper->getString("Description");
        $resultWrapper->imageFileName = $sourceWrapper->getString("ImageFileName");
        $resultWrapper->imageThumbnailName = $sourceWrapper->getString("ImageThumbnailName");
        $resultWrapper->isReservable = $sourceWrapper->getBool("IsReservable");
        $resultWrapper->inventoryTolerance = $sourceWrapper->getInt("InventoryTolerance");
        $resultWrapper->info = $this->mapper->map($sourceWrapper->get("TextbookInfo", []), BookInfo::class);
        $resultWrapper->products = array_map(function ($item) {
            return $this->mapper->map($item, Product::class);
        }, $sourceWrapper->get("Products") ?: []);

        return $result;
    }
}
