<?php

namespace FMT\DomainBundle\Type\Cart;

use FMT\InfrastructureBundle\Helper\CurrencyHelper;

/**
 * Class Summary
 * @package FMT\DomainBundle\Type\Cart
 */
class Summary
{
    /**
     * @var int
     */
    private $itemsCount;

    /**
     * @var int
     */
    private $subtotal;

    /**
     * @var int
     */
    private $shipping;

    /**
     * @var int
     */
    private $tax;

    /**
     * @var int
     */
    private $fmtFee;

    /**
     * @var int
     */
    private $transactionFee;

    /**
     * @var int
     */
    private $total;

    /**
     * @return int
     */
    public function getItemsCount()
    {
        return $this->itemsCount;
    }

    /**
     * @param int $itemsCount
     * @return $this
     */
    public function setItemsCount($itemsCount)
    {
        $this->itemsCount = $itemsCount;
        return $this;
    }

    /**
     * @return int
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * @param int $subtotal
     * @return $this
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;
        return $this;
    }

    /**
     * @return int
     */
    public function getShipping()
    {
        return $this->shipping;
    }

    /**
     * @param int $shipping
     * @return $this
     */
    public function setShipping($shipping)
    {
        $this->shipping = $shipping;
        return $this;
    }

    /**
     * @return int
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * @param int $tax
     * @return $this
     */
    public function setTax($tax)
    {
        $this->tax = $tax;
        return $this;
    }

    /**
     * @return int
     */
    public function getFmtFee()
    {
        return $this->fmtFee;
    }

    /**
     * @param int $fmtFee
     * @return $this
     */
    public function setFmtFee($fmtFee)
    {
        $this->fmtFee = $fmtFee;
        return $this;
    }

    /**
     * @return int
     */
    public function getTransactionFee()
    {
        return $this->transactionFee;
    }

    /**
     * @param int $transactionFee
     * @return $this
     */
    public function setTransactionFee($transactionFee)
    {
        $this->transactionFee = $transactionFee;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param int $total
     * @return $this
     */
    public function setTotal($total)
    {
        $this->total = $total;
        return $this;
    }

    /**
     * @return array
     */
    public function getArray()
    {
        return [
            'itemsCount' => $this->getItemsCount(),
            'subtotal' => $this->getSubtotal(),
            'shipping' => $this->getShipping(),
            'tax' => $this->getTax(),
            'fmtFee' => $this->getFmtFee(),
            'transactionFee' => $this->getTransactionFee(),
            'total' => $this->getTotal(),
        ];
    }

    /**
     * @return array
     */
    public function getFormattedArray()
    {
        $summary = $this->getArray();

        foreach ($this->priceFormattedFields() as $field) {
            $summary[$field] = CurrencyHelper::priceFilter($summary[$field]);
        }

        return $summary;
    }

    private function priceFormattedFields()
    {
        return [
            'subtotal',
            'shipping',
            'tax',
            'fmtFee',
            'transactionFee',
            'total',
        ];
    }
}
