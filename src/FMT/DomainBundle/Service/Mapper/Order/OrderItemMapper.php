<?php
/**
 * Author: Vladimir Bykovsky
 * Date: 25.01.2022
 * Time: 15:10
 */

namespace FMT\DomainBundle\Service\Mapper\Order;

use FMT\DataBundle\Entity\OrderItem as EntityOrderItem;
use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Nebook\SoapApi\Item\OrderItem;
use FMT\InfrastructureBundle\Service\Nebook\SoapApi\Item\Product;

/**
 * OrderItemMapper
 */
class OrderItemMapper
{    
    /**
     * @param EntityOrderItem $source
     * @return OrderItem
     */
    public static function map(EntityOrderItem $source) : OrderItem
    {
        $result = new OrderItem();
        $resultWrapper = new DataHelper($result);

        $resultWrapper->product = ProductMapper::map($source->getBook(), Product::class);
        $resultWrapper->storeRetailPrice = $source->getPrice() / 100;
        $resultWrapper->costOfGoods = $source->getPrice() / 100;

        return $result;
    }
}
