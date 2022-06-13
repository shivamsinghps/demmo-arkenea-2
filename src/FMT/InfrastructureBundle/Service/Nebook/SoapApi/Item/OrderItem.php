<?php
/**
 * Author: Vladimir Bykovsky
 * Date: 11.11.2021
 * Time: 15:10
 */

namespace FMT\InfrastructureBundle\Service\Nebook\SoapApi\Item;

/**
 * Class OrderItem
 */
class OrderItem
{

    /** @var Product */
    private $product;

    /** @var string */
    private $storeRetailPrice;

    /** @var string */
    private $costOfGoods;

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param Product $product
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * @return string
     */
    public function getStoreRetailPrice()
    {
        return $this->storeRetailPrice;
    }

    /**
     * @param string $storeRetailPrice
     */
    public function setStoreRetailPrice($storeRetailPrice)
    {
        $this->storeRetailPrice = $storeRetailPrice;
    }

    /**
     * @return string
     */
    public function getCostOfGoods()
    {
        return $this->costOfGoods;
    }

    /**
     * @param string $costOfGoods
     */
    public function setCostOfGoods($costOfGoods)
    {
        $this->costOfGoods = $costOfGoods;
    }
}
