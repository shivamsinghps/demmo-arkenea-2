<?php

namespace FMT\DataBundle\Mapper;

use FMT\DataBundle\Entity\CampaignBook;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\Product;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\ProductFamily;

class CampaignBookMapper
{
    public static function map(CampaignBook $book, ProductFamily $productFamily, Product $product) : CampaignBook
    {
        $book->setTitle($productFamily->getName());

        $author = $productFamily->getInfo() ? $productFamily->getInfo()->getAuthor() : '';
        $book->setAuthor($author);

        $isbn = $productFamily->getInfo() ? $productFamily->getInfo()->getIsbn() : '';
        $book->setIsbn($isbn);

        $book->setPrice($product->getPrice());

        $states = array_keys(array_filter([
            CampaignBook::STATE_NEW => $product->isNew(),
            CampaignBook::STATE_USED => $product->isUsed(),
            CampaignBook::STATE_UNKNOWN => true,
        ]));

        $book->setState(array_shift($states));

        $statuses = array_keys(array_filter([
            CampaignBook::STATUS_ORDERED => $book->getStatus() == CampaignBook::STATUS_ORDERED,
            CampaignBook::STATUS_OUT_OF_STOCK => $product->getCalculatedInventory() == 0,
            CampaignBook::STATUS_AVAILABLE => true,
        ]));

        $book->setStatus(array_shift($statuses));

        return $book;
    }
}
