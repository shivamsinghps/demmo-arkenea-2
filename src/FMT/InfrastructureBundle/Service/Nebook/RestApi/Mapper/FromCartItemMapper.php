<?php
/**
 * Author: Anton Orlov
 * Date: 05.03.2018
 * Time: 11:09
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\CartItem;

class FromCartItemMapper extends AbstractMapper
{
    public function map(CartItem $source) : array
    {
        $result = [];

        $result["__type"] = "CartTextbookItemEdit:#CampusHub.WebPrism.Api.DataContracts.Dto.Request.WebPrism";
        $result["CatalogId"] = $source->getCatalogId();
        $result["GroupId"] = $source->getGroupId();
        $result["IsRental"] = $source->isRental();
        $result["Price"] = $this->fromIntPrice($source->getPrice());
        $result["ProductFamilyId"] = $source->getFamilyId();
        $result["Quantity"] = $source->getQuantity();
        $result["SectionId"] = $source->getSectionId();
        $result["Sku"] = $source->getSku();
        $result["AllowSubstitution"] = $source->isAllowSubstitution();

        return $result;
    }
}
