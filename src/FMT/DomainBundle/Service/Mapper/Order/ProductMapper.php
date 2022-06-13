<?php
/**
 * Author: Vladimir Bykovsky
 * Date: 25.01.2022
 * Time: 15:10
 */

namespace FMT\DomainBundle\Service\Mapper\Order;

use FMT\DataBundle\Entity\CampaignBook;
use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Nebook\SoapApi\Item\Product;

/**
 * ProductMapper
 */
class ProductMapper
{  
    /**
     * @var string const BOOK_TYPE
     */
    const BOOK_TYPE = 'Trade';

    /**
     * @param CampaignBook $source
     * @return Product
     */
    public static function map(CampaignBook $source) : Product
    {
        $result = new Product();
        $resultWrapper = new DataHelper($result);

        $resultWrapper->id = $source->getProductFamilyId();
        $resultWrapper->type = self::BOOK_TYPE;
        $resultWrapper->sku = $source->getSku();
        $resultWrapper->title = $source->getTitle();
        $resultWrapper->isbn = $source->getIsbn();
        $resultWrapper->newUsed = '';
        $resultWrapper->qty = $source->getQuantity();
        $resultWrapper->price = $source->getPrice();
        $resultWrapper->acctCost = $source->getQuantity() * $source->getPrice();
        $resultWrapper->guide = '';
        $resultWrapper->guideRetail = '';
        $resultWrapper->adopted = '';
        $resultWrapper->term = '';

        return $result;
    }
}
