<?php
/**
 * Author: Vladimir Bykovsky
 * Date: 11.11.2021
 * Time: 15:10
 */

namespace FMT\InfrastructureBundle\Service\Nebook\SoapApi\Mapper;

use FMT\InfrastructureBundle\Service\Nebook\AbstractMapper;
use FMT\InfrastructureBundle\Service\Nebook\SoapApi\Item\Order;
use FMT\InfrastructureBundle\Service\Nebook\Mapper;

/**
 * OrderMapper
 */
class OrderMapper extends AbstractMapper
{    
    /**
     * @param  Order $source
     * @return array
     */
    public function map(Order $source): array
    {
        if (!$source->getShipping() || !$source->getShipping()->getAddress()) {
            return [];
        }

        $result = [
            "ShippingID" => $source->getShipping()->getId(),
            "ShippingAmount" => $source->getShipping()->getAmount(),
            "TaxExempt" => $source->getTaxExempt(),
            "TaxAmount" => $source->getTaxAmount(),
            "CommissionAmount" => $source->getCommissionAmount(),
            "Marketplace" => $source->getMarketplace(),
            "ShippingInfo" => [],
            "ShippingInstr" => $source->getShipping()->getInstr(),
            "MonsoonOrderNumber" => $source->getMonsoonOrderNumber(),
            "MarketplaceOrderNumber" => $source->getMarketplaceOrderNumber(),
            "OrderDate" => $source->getOrderDate(),
            "ShipDate" => $source->getShipDate(),
            "BuyerEmail" => $source->getBuyerEmail(),
            "ActualPostage" => $source->getActualPostage(),
            "TrackingNumber" => $source->getTrackingNumber(),
            "OrderInputItems" => []
        ];

        $address = $source->getShipping()->getAddress();
        $result["ShippingInfo"] = Mapper::map($address, 'array', Mapper::DIR_CLIENT_SOAP_API);

        foreach ($source->getItems() as $orderItem) {
            $temp = Mapper::map($orderItem->getProduct(), 'array', Mapper::DIR_CLIENT_SOAP_API);
            $temp['StoreRetailPrice'] = $orderItem->getStoreRetailPrice();
            $temp['CostOfGoods'] = $orderItem->getCostOfGoods();
            $result["OrderInputItems"]["PushOrderInputItem"][] = $temp;
        }

        return $result;
    }
}
