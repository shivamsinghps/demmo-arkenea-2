<?php
/**
 * Author: Anton Orlov
 * Date: 05.03.2018
 * Time: 16:22
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\OrderItem;

class OrderItemMapper extends AbstractMapper
{
    public function map(array $source) : OrderItem
    {
        $result = new OrderItem();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->sku = $sourceWrapper->getString("Product.Sku");
        $resultWrapper->isbn = $sourceWrapper->getString("Product.Isbn");
        $resultWrapper->backofficeId = $sourceWrapper->getString("Product.BackofficeProductId");
        $resultWrapper->pfId = $sourceWrapper->getString("Product.PfId");
        $resultWrapper->name = $sourceWrapper->getString("Product.Name");
        $resultWrapper->attributes = $sourceWrapper->get("Product.Attributes", []);
        $resultWrapper->term = $sourceWrapper->getString("CourseInfo.Term");
        $resultWrapper->department = $sourceWrapper->getString("CourseInfo.Dept");
        $resultWrapper->course = $sourceWrapper->getString("CourseInfo.Course");
        $resultWrapper->section = $sourceWrapper->getString("CourseInfo.Section");
        $resultWrapper->regNumber = $sourceWrapper->getString("CourseInfo.RegistrationNumber");
        $resultWrapper->type = $sourceWrapper->getInt("Product.Type");
        $resultWrapper->status = $sourceWrapper->getString("Status");
        $resultWrapper->quantity = $sourceWrapper->getInt("Quantity");
        $resultWrapper->listPrice = $this->toIntPrice($sourceWrapper->getString("ListPrice"));
        $resultWrapper->placedPrice = $this->toIntPrice($sourceWrapper->getString("PlacedPrice"));
        $resultWrapper->itemPrice = $this->toIntPrice($sourceWrapper->getString("ItemPrice"));
        $resultWrapper->itemTotal = $this->toIntPrice($sourceWrapper->getString("ItemTotal"));
        $resultWrapper->itemTax = $this->toIntPrice($sourceWrapper->getString("ItemTax"));
        $resultWrapper->shippingTax = $this->toIntPrice($sourceWrapper->getString("ShippingTax"));
        $resultWrapper->taxTotal = $this->toIntPrice($sourceWrapper->getString("TaxTotal"));
        $resultWrapper->requestedCondition = $sourceWrapper->getString("RequestedCondition");
        $resultWrapper->packingComment = $sourceWrapper->getString("PackingComment");
        $resultWrapper->rentalAgreement = $sourceWrapper->getString("RentalAgreement");
        $resultWrapper->dueDate = $sourceWrapper->getDate("DueDate");

        return $result;
    }
}
