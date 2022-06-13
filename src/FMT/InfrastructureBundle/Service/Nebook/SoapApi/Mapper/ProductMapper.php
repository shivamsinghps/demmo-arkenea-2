<?php

/**
 * Author: Vladimir Bykovsky
 * Date: 20.12.2021
 * Time: 15:10
 */

namespace FMT\InfrastructureBundle\Service\Nebook\SoapApi\Mapper;

use FMT\InfrastructureBundle\Service\Nebook\AbstractMapper;
use FMT\InfrastructureBundle\Service\Nebook\SoapApi\Item\Product;

/**
 * ProductMapper
 */
class ProductMapper extends AbstractMapper
{    
    /**
     * @param  Product $source
     * @return array
     */
    public function map(Product $source): array
    {
        return [
            "ProductID" => $source->getId(),
            'ProductType' => $source->getType(),
            'ProductSku' => $source->getSku(),
            'ProductTitle' => $source->getTitle(),
            'ProductISBN' => $source->getIsbn(),
            'ProductNewUsed' => $source->getNewUsed(),
            'ProductQTY' => $source->getQty(),
            'ProductPrice' => $source->getPrice(),
            'ProductAcctCost' => $source->getAcctCost(),
            'ProductGuide' => $source->getGuide(),
            'ProductGuideRetail' => $source->getGuideRetail(),
            'ProductAdopted' => $source->getAdopted(),
            "ProductTerm" => $source->getTerm()
        ];
    }
}
