<?php
/**
 * Author: Vladimir Bykovsky
 * Date: 11.11.2021
 * Time: 15:10
 */

namespace FMT\InfrastructureBundle\Service\Nebook\SoapApi\Item;

/**
 * Class Order
 */
class Order
{
    /** @var bool */
    private $taxExempt;

    /** @var string */
    private $taxAmount;

    /** @var string */
    private $commissionAmount;

    /** @var string */
    private $marketplace;

    /** @var Shipping */
    private $shipping;

    /** @var string */
    private $monsoonOrderNumber;

    /** @var string */
    private $marketplaceOrderNumber;

    /** @var string */
    private $orderDate;

    /** @var string */
    private $shipDate;

    /** @var string */
    private $buyerEmail;

    /** @var string */
    private $actualPostage;

    /** @var string */
    private $trackingNumber;

    /** @var OrderItem[] */
    private $items;

    public function __construct()
    {
        $this->items = [];
    }

    /**
     * @return bool
     */
    public function getTaxExempt()
    {
        return $this->taxExempt;
    }

    /**
     * @param bool $taxExempt
     */
    public function setTaxExempt($taxExempt)
    {
        $this->taxExempt = $taxExempt;
    }

    /**
     * @return string
     */
    public function getTaxAmount()
    {
        return $this->taxAmount;
    }

    /**
     * @param string $taxAmount
     */
    public function setTaxAmount($taxAmount)
    {
        $this->taxAmount = $taxAmount;
    }

    /**
     * @return string
     */
    public function getCommissionAmount()
    {
        return $this->commissionAmount;
    }

    /**
     * @param string $commissionAmount
     */
    public function setCommissionAmount($commissionAmount)
    {
        $this->commissionAmount = $commissionAmount;
    }

    /**
     * @return string
     */
    public function getMarketplace()
    {
        return $this->marketplace;
    }

    /**
     * @param string $marketplace
     */
    public function setMarketplace($marketplace)
    {
        $this->marketplace = $marketplace;
    }

    /**
     * @return Shipping
     */
    public function getShipping(): Shipping
    {
        return $this->shipping;
    }

    /**
     * @param Shipping $shipping
     */
    public function setShipping(Shipping $shipping)
    {
        $this->shipping = $shipping;
    }

    /**
     * @return string
     */
    public function getMonsoonOrderNumber()
    {
        return $this->monsoonOrderNumber;
    }

    /**
     * @param string $monsoonOrderNumber
     */
    public function setMonsoonOrderNumber($monsoonOrderNumber)
    {
        $this->monsoonOrderNumber = $monsoonOrderNumber;
    }

    /**
     * @return string
     */
    public function getMarketplaceOrderNumber()
    {
        return $this->marketplaceOrderNumber;
    }

    /**
     * @param string $marketplaceOrderNumber
     */
    public function setMarketplaceOrderNumber($marketplaceOrderNumber)
    {
        $this->marketplaceOrderNumber = $marketplaceOrderNumber;
    }

    /**
     * @return string
     */
    public function getOrderDate()
    {
        return $this->orderDate;
    }

    /**
     * @param string $orderDate
     */
    public function setOrderDate($orderDate)
    {
        $this->orderDate = $orderDate;
    }

    /**
     * @return string
     */
    public function getShipDate()
    {
        return $this->shipDate;
    }

    /**
     * @param string $shipDate
     */
    public function setShipDate($shipDate)
    {
        $this->shipDate = $shipDate;
    }

    /**
     * @return string
     */
    public function getBuyerEmail()
    {
        return $this->buyerEmail;
    }

    /**
     * @param string $buyerEmail
     */
    public function setBuyerEmail($buyerEmail)
    {
        $this->buyerEmail = $buyerEmail;
    }

    /**
     * @return string
     */
    public function getActualPostage()
    {
        return $this->actualPostage;
    }

    /**
     * @param string $actualPostage
     */
    public function setActualPostage($actualPostage)
    {
        $this->actualPostage = $actualPostage;
    }

    /**
     * @return string
     */
    public function getTrackingNumber()
    {
        return $this->trackingNumber;
    }

    /**
     * @param string $trackingNumber
     */
    public function setTrackingNumber($trackingNumber)
    {
        $this->trackingNumber = $trackingNumber;
    }

    /**
     * @return OrderItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param OrderItem $item
     */
    public function addItems(OrderItem $item)
    {
        $this->items[] = $item;
    }
}
