<?php

/**
 * Created by Karina Kalina
 * Date: 24.04.18
 * Time: 14:57
 */

namespace FMT\DomainBundle\Service\Mapper\Campaign\Book;

use FMT\DomainBundle\Type\Campaign\Book\Product;
use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Product as NebookProduct;

class ProductMapper
{
    public static function map(NebookProduct $source) : Product
    {
        $result = new Product();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->sku = $sourceWrapper->getString("sku");
        $resultWrapper->state = ucfirst(strtolower($sourceWrapper->getString("state")));
        $resultWrapper->price = $sourceWrapper->getInt("price");
        $resultWrapper->calculatedInventory = $sourceWrapper->getInt("calculatedInventory");

        return $result;
    }
}
