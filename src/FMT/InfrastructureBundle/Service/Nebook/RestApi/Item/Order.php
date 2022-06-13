<?php
/**
 * Author: Anton Orlov
 * Date: 05.03.2018
 * Time: 15:32
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Item;

/**
 * Class Order
 * @package FMT\InfrastructureBundle\Service\Nebook\RestApi\Item
 *
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Order
{
    const TYPE_PURCHASE = 1;
    const TYPE_RESERVER = 2;
    const TYPE_VENDOR = 3;

    /** @var int */
    private $id;

    /** @var string */
    private $uniqueId;

    /** @var string */
    private $studentId;

    /** @var string */
    private $memberId;

    /** @var int */
    private $type;

    /** @var string */
    private $status;

    /** @var \DateTime */
    private $created;

    /** @var \DateTime */
    private $modified;

    /** @var string */
    private $shipToEmail;

    /** @var string */
    private $referringUrl;

    /** @var int */
    private $subTotal;

    /** @var int */
    private $shippingTotal;

    /** @var int */
    private $taxTotal;

    /** @var int */
    private $total;

    /** @var int */
    private $shippingCodeId;

    /** @var  string */
    private $shippingCodeName;

    /** @var string */
    private $shippingInstructions;

    /** @var Order */
    private $original;

    /** @var Address */
    private $billing;

    /** @var OrderPaymentMethod[] */
    private $paymentMethods;

    /** @var Address */
    private $shipping;

    /** @var OrderItem[] */
    private $items;

    /** @var OrderComment[] */
    private $comments;

    /** @var Shopper */
    private $shopper;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUniqueId()
    {
        return $this->uniqueId;
    }

    /**
     * @return string
     */
    public function getStudentId()
    {
        return $this->studentId;
    }

    /**
     * @return string
     */
    public function getMemberId()
    {
        return $this->memberId;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return \DateTime
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * @return string
     */
    public function getShipToEmail()
    {
        return $this->shipToEmail;
    }

    /**
     * @return string
     */
    public function getReferringUrl()
    {
        return $this->referringUrl;
    }

    /**
     * @return int
     */
    public function getSubTotal()
    {
        return $this->subTotal;
    }

    /**
     * @return int
     */
    public function getShippingTotal()
    {
        return $this->shippingTotal;
    }

    /**
     * @return int
     */
    public function getTaxTotal()
    {
        return $this->taxTotal;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @return int
     */
    public function getShippingCodeId()
    {
        return $this->shippingCodeId;
    }

    /**
     * @return string
     */
    public function getShippingCodeName()
    {
        return $this->shippingCodeName;
    }

    /**
     * @return string
     */
    public function getShippingInstructions()
    {
        return $this->shippingInstructions;
    }

    /**
     * @return Order
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * @return Address
     */
    public function getBilling()
    {
        return $this->billing;
    }

    /**
     * @return OrderPaymentMethod[]
     */
    public function getPaymentMethods()
    {
        return $this->paymentMethods;
    }

    /**
     * @return Address
     */
    public function getShipping()
    {
        return $this->shipping;
    }

    /**
     * @return OrderItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return OrderComment[]
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @return Shopper
     */
    public function getShopper()
    {
        return $this->shopper;
    }
}
