<?php
/**
 * Author: Anton Orlov
 * Date: 28.02.2018
 * Time: 17:56
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Item;

class Product
{
    const STATE_NEW = 'new';
    const STATE_USED = 'used';

    /** @var string */
    private $sku;

    /** @var string */
    private $upc;

    /** @var string */
    private $backofficeId;

    /** @var array */
    private $attributes;

    /** @var int */
    private $inventory;

    /** @var int */
    private $calculatedInventory;

    /** @var int */
    private $price;

    /** @var int */
    private $listPrice;

    /** @var int */
    private $accountingCost;

    /** @var bool */
    private $taxable;

    /** @var bool */
    private $shippingCostOverridden;

    /** @var int */
    private $shippingCostOverrideAmount;

    /** @var \DateTime */
    private $saleStart;

    /** @var \DateTime */
    private $saleEnd;

    /** @var string */
    private $onOrder;

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @return string
     */
    public function getUpc()
    {
        return $this->upc;
    }

    /**
     * @return string
     */
    public function getBackofficeId()
    {
        return $this->backofficeId;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return int
     */
    public function getInventory()
    {
        return $this->inventory;
    }

    /**
     * @return int
     */
    public function getCalculatedInventory()
    {
        return $this->calculatedInventory;
    }

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return int
     */
    public function getListPrice()
    {
        return $this->listPrice;
    }

    /**
     * @return int
     */
    public function getAccountingCost()
    {
        return $this->accountingCost;
    }

    /**
     * @return boolean
     */
    public function isTaxable()
    {
        return $this->taxable;
    }

    /**
     * @return boolean
     */
    public function isShippingCostOverridden()
    {
        return $this->shippingCostOverridden;
    }

    /**
     * @return int
     */
    public function getShippingCostOverrideAmount()
    {
        return $this->shippingCostOverrideAmount;
    }

    /**
     * @return \DateTime
     */
    public function getSaleStart()
    {
        return $this->saleStart;
    }

    /**
     * @return \DateTime
     */
    public function getSaleEnd()
    {
        return $this->saleEnd;
    }

    /**
     * @return string
     */
    public function getOnOrder()
    {
        return $this->onOrder;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->attributes['Type'] ?? '';
    }

    /**
     * @return string
     */
    public function isNew()
    {
        return strtolower($this->getState()) == self::STATE_NEW;
    }

    /**
     * @return string
     */
    public function isUsed()
    {
        return strtolower($this->getState()) == self::STATE_USED;
    }
}
