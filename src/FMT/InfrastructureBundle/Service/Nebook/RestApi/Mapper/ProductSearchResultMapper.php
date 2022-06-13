<?php
/**
 * Author: Anton Orlov
 * Date: 01.03.2018
 * Time: 11:04
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Mapper;

use FMT\InfrastructureBundle\Helper\DataHelper;
use FMT\InfrastructureBundle\Service\Mapper\AbstractMapper;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\ProductSearchItem;
use FMT\InfrastructureBundle\Service\Nebook\RestApi\Item\ProductSearchResult;

class ProductSearchResultMapper extends AbstractMapper
{
    public function map(array $source) : ProductSearchResult
    {
        $result = new ProductSearchResult();
        $sourceWrapper = new DataHelper($source);
        $resultWrapper = new DataHelper($result);

        $resultWrapper->page = $sourceWrapper->getInt("CurrentPage");
        $resultWrapper->pageSize = $sourceWrapper->getInt("PageSize");
        $resultWrapper->totalPages = $sourceWrapper->getInt("TotalPages");
        $resultWrapper->totalCount = $sourceWrapper->getInt("TotalCount");
        $resultWrapper->searchText = $sourceWrapper->getString("SearchText");
        $resultWrapper->searchResults = array_map(function ($item) {
            return $this->mapper->map($item, ProductSearchItem::class);
        }, $sourceWrapper->get("Products"));

        return $result;
    }
}
