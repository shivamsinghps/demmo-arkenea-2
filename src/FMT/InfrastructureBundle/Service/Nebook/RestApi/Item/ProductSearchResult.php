<?php
/**
 * Author: Anton Orlov
 * Date: 01.03.2018
 * Time: 10:13
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Item;

class ProductSearchResult
{
    /** @var int */
    private $page;

    /** @var int */
    private $pageSize;

    /** @var int */
    private $totalPages;

    /** @var int */
    private $totalCount;

    /** @var string */
    private $searchText;

    /** @var ProductSearchItem[] */
    private $searchResults;

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * @return int
     */
    public function getTotalPages()
    {
        return $this->totalPages;
    }

    /**
     * @return int
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * @return string
     */
    public function getSearchText()
    {
        return $this->searchText;
    }

    /**
     * @return ProductSearchItem[]
     */
    public function getSearchResults()
    {
        return $this->searchResults;
    }
}
