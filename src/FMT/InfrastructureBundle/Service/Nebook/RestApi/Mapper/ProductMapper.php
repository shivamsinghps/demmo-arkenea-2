<?php
/**
 * Author: Anton Orlov
 * Date: 28.02.2018
 * Time: 18:11
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Product;

class ProductMapper extends AbstractMapper
{
    public function map(array $source) : Product
    {
        $result = new Product();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->sku = $sourceWrapper->getString("Sku");
        $resultWrapper->upc = $sourceWrapper->getString("Upc");
        $resultWrapper->backofficeId = $sourceWrapper->getString("BackofficeId");
        $resultWrapper->attributes = $this->mapNvpToDict($sourceWrapper->get("Attributes", []));
        $resultWrapper->inventory = $sourceWrapper->getInt("Inventory");
        $resultWrapper->calculatedInventory = $sourceWrapper->getInt("CalculatedInventory");
        $resultWrapper->price = $this->toIntPrice($sourceWrapper->getString("Price"));
        $resultWrapper->listPrice = $this->toIntPrice($sourceWrapper->getString("ListPrice"));
        $resultWrapper->accountingCost = $this->toIntPrice($sourceWrapper->getString("AccountingCost"));
        $resultWrapper->isTaxable = $sourceWrapper->getBool("IsTaxable");
        $resultWrapper->isShippingCostOverridden = $sourceWrapper->getBool("ShippingCostOverridden");
        $resultWrapper->shippingCostOverrideAmount = $this->toIntPrice(
            $sourceWrapper->getString("ShippingOverrideAmount")
        );
        $resultWrapper->saleStart = $sourceWrapper->getDate("SaleStart");
        $resultWrapper->saleEnd = $sourceWrapper->getDate("SaleEnd");
        $resultWrapper->onOrder = $sourceWrapper->getString("OnOrder");

        return $result;
    }
}
