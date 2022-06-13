<?php
/**
 * Author: Anton Orlov
 * Date: 05.03.2018
 * Time: 13:24
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\CartItem;

class CartItemMapper extends AbstractMapper
{
    public function map(array $source) : CartItem
    {
        $result = new CartItem();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->groupId = $sourceWrapper->getInt("GroupId");
        $resultWrapper->familyId = $sourceWrapper->getString("Product.ProductFamilyId");
        $resultWrapper->sku = $sourceWrapper->getString("Product.Sku");
        $resultWrapper->quantity = $sourceWrapper->getInt("Quantity");
        $resultWrapper->price = $this->toIntPrice($sourceWrapper->getString("Price"));
        $resultWrapper->itemAttributes = $sourceWrapper->get("Attributes", []);
        $resultWrapper->productAttributes = $this->mapNvpToDict($sourceWrapper->get("Product.Attributes", []));
        $resultWrapper->name = $sourceWrapper->getString("Product.Name");
        $resultWrapper->description = $sourceWrapper->getString("Product.Description");
        $resultWrapper->isbn = $sourceWrapper->getString("Product.Isbn");
        $resultWrapper->imageFile = $sourceWrapper->getString("Product.ImageFile");
        $resultWrapper->imageThumbnail = $sourceWrapper->getString("Product.ImageFileThumbnail");
        $resultWrapper->textbook = $sourceWrapper->getBool("Product.IsTextbook");
        $resultWrapper->purchaseType = $sourceWrapper->getInt("PurchaseType");

        return $result;
    }
}
