<?php
/**
 * Author: Vladimir Bykovsky
 * Date: 25.11.2021
 * Time: 18:00
 */

namespace FMT\DomainBundle\Service\Mapper\Order;

use FMT\DataBundle\Entity\Order as EntityOrder;
use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Nebook\SoapApi\Item\Order;
use FMT\InfrastructureBundle\Service\Nebook\SoapApi\Item\Shipping;
use FMT\InfrastructureBundle\Service\Nebook\SoapApi\Item\OrderItem;

/**
 * OrderMapper
 */
class OrderMapper 
{
    /**
     * @var string const MARKETPLACE
     */
    const MARKETPLACE = 'FundMyTextbooks';
    
    /**
     * @param EntityOrder $source
     * @return Order
     */
    public static function map(EntityOrder $source) : Order
    {
        $result = new Order();
        $resultWrapper = new DataHelper($result);

        $user = $source->getCampaign()->getUser();

        $resultWrapper->taxExempt = false;
        $resultWrapper->taxAmount = $source->getTax() / 100;
        $resultWrapper->commissionAmount = $source->getTransactionFee() / 100;
        $resultWrapper->marketplace = self::MARKETPLACE;
        $resultWrapper->shipping = ShippingMapper::map($source, Shipping::class);
        $resultWrapper->monsoonOrderNumber = '';
        $resultWrapper->marketplaceOrderNumber = $source->getId();
        $resultWrapper->orderDate = (new \DateTime())->format('Y-m-d');
        $resultWrapper->shipDate = '';
        $resultWrapper->buyerEmail = $user->getProfile()->getEmail();
        $resultWrapper->actualPostage = '';
        $resultWrapper->trackingNumber = '';

        $items = [];
        foreach($source->getItems() as $sourceOrderItem) {
            $items[] = OrderItemMapper::map($sourceOrderItem, OrderItem::class);
        }
        $resultWrapper->items = $items;

        return $result;
    }
}
