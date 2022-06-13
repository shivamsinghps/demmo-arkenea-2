<?php
/**
 * Author: Anton Orlov
 * Date: 05.03.2018
 * Time: 13:07
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Item;

class AddItemsResult
{
    /** @var AddItemResult[] */
    private $results;

    /** @var CartSummary */
    private $summary;

    /**
     * @return AddItemResult[]
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @return CartSummary
     */
    public function getSummary()
    {
        return $this->summary;
    }
}
