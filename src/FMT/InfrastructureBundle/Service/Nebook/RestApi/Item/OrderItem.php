<?php
/**
 * Author: Anton Orlov
 * Date: 05.03.2018
 * Time: 15:57
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Item;

/**
 * Class OrderItem
 * @package FMT\InfrastructureBundle\Service\Nebook\RestApi\Item
 *
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class OrderItem
{
    const TYPE_UNKNOWN = "Unknown";
    const TYPE_GENERAL_MERCHANDISE = "GeneralMerchandise";
    const TYPE_TEXTBOOK = "Textbook";
    const TYPE_TRADEBOOK = "Tradebook";
    const TYPE_PACKAGE = "Package";
    const TYPE_TEXTREQ = "Textreq";
    const TYPE_EBOOK = "Ebook";
    const TYPE_BARCHARTS = "Barcharts";

    public const STATUS_SUBMITTED = 'Submitted';
    public const STATUS_ACCEPTED = 'Accepted';
    public const STATUS_PARTIALLY_SHIPPED = 'Partially Shipped';
    public const STATUS_SHIPPED = 'Shipped';
    public const STATUS_DELETED = 'Deleted';
    public const STATUS_CANCELED = 'Canceled';
    public const STATUS_RETURNED = 'Returned';

    /** @var string */
    private $sku;

    /** @var string */
    private $isbn;

    /** @var string */
    private $backofficeId;

    /** @var string */
    private $pfId;

    /** @var string */
    private $name;

    /** @var string[] */
    private $attributes;

    /** @var string */
    private $term;

    /** @var string */
    private $department;

    /** @var string */
    private $course;

    /** @var string */
    private $section;

    /** @var string */
    private $regNumber;

    /** @var int */
    private $type;

    /** @var string */
    private $status;

    /** @var int */
    private $quantity;

    /** @var int */
    private $listPrice;

    /** @var int */
    private $placedPrice;

    /** @var int */
    private $itemPrice;

    /** @var int */
    private $itemTotal;

    /** @var int */
    private $itemTax;

    /** @var int */
    private $shippingTax;

    /** @var int */
    private $taxTotal;

    /** @var string */
    private $requestedCondition;

    /** @var string */
    private $packingComment;

    /** @var string */
    private $rentalAgreement;

    /** @var \DateTime */
    private $dueDate;

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
    public function getIsbn()
    {
        return $this->isbn;
    }

    /**
     * @return string
     */
    public function getBackofficeId()
    {
        return $this->backofficeId;
    }

    /**
     * @return string
     */
    public function getPfId()
    {
        return $this->pfId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \string[]
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return string
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * @return string
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @return string
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * @return string
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * @return string
     */
    public function getRegNumber()
    {
        return $this->regNumber;
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
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
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
    public function getPlacedPrice()
    {
        return $this->placedPrice;
    }

    /**
     * @return int
     */
    public function getItemPrice()
    {
        return $this->itemPrice;
    }

    /**
     * @return int
     */
    public function getItemTotal()
    {
        return $this->itemTotal;
    }

    /**
     * @return int
     */
    public function getItemTax()
    {
        return $this->itemTax;
    }

    /**
     * @return int
     */
    public function getShippingTax()
    {
        return $this->shippingTax;
    }

    /**
     * @return int
     */
    public function getTaxTotal()
    {
        return $this->taxTotal;
    }

    /**
     * @return string
     */
    public function getRequestedCondition()
    {
        return $this->requestedCondition;
    }

    /**
     * @return string
     */
    public function getPackingComment()
    {
        return $this->packingComment;
    }

    /**
     * @return string
     */
    public function getRentalAgreement()
    {
        return $this->rentalAgreement;
    }

    /**
     * @return \DateTime
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }
}
