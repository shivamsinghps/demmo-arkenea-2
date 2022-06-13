<?php

/**
 * Created by Karina Kalina
 * Date: 24.04.18
 * Time: 14:57
 */

namespace FMT\DomainBundle\Service\Mapper\Campaign\Book;

use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\ProductFamily as NebookFamily;

class FamilyMapper
{
    public static function map(NebookFamily $source) : array
    {
        $arr = [];
        $sourceWrapper = new DataHelper($source);

        foreach ($source->getProducts() as $product) {
            $newProduct = ProductMapper::map($product);
            $resultWrapper = new DataHelper($newProduct);

            $resultWrapper->familyId = $sourceWrapper->getInt("id");
            $resultWrapper->name = $sourceWrapper->getString("name");
            $resultWrapper->author = $sourceWrapper->get("info.author");
            $resultWrapper->isbn = $sourceWrapper->get("info.isbn");

            $arr[] = $newProduct;
        }

        return $arr;
    }
}
