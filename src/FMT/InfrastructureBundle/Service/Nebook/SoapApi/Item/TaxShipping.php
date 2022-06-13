<?php
/**
 * Author: Vladimir Bykovsky
 * Date: 11.11.2021
 * Time: 15:10
 */

namespace FMT\InfrastructureBundle\Service\Nebook\SoapApi\Item;

/**
 * Class TaxShipping
 */
class TaxShipping
{

    /** @var double */
    private $taxAmount;

    /** @var int */
    private $textbookTaxAmount;

    /** @var int */
    private $taxShipInd;

    /** @var ShippingCode */
    private $shippingCodes;

    public function __construct()
    {
        $this->shippingCodes = [];
    }
    
    /**
     * @return int
     */
    public function getTaxAmount()
    {
        return $this->taxAmount;
    }

    /**
     * @param int $taxAmount
     */
    public function setTaxAmount($taxAmount)
    {
        $this->taxAmount = $taxAmount;
    }

    /**
     * @return int
     */
    public function getTextbookTaxAmount()
    {
        return $this->textbookTaxAmount;
    }

    /**
     * @param int $textbookTaxAmount
     */
    public function setTextbookTaxAmount($textbookTaxAmount)
    {
        $this->textbookTaxAmount = $textbookTaxAmount;
    }

    /**
     * @return double
     */
    public function getTaxShipInd()
    {
        return $this->taxShipInd;
    }

    /**
     * @param double $taxShipInd
     */
    public function setTaxShipInd($taxShipInd)
    {
        $this->taxShipInd = $taxShipInd;
    }

    /**
     * @return ShippingCode[]
     */
    public function getShippingCodes()
    {
        return $this->shippingCodes;
    }

    /**
     * @param ShippingCode $shippingCode
     */
    public function addShippingCodes(ShippingCode $shippingCode)
    {
        $this->shippingCodes[] = $shippingCode;
    }
}
