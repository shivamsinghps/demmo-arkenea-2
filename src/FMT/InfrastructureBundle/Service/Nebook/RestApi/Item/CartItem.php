<?php
/**
 * Author: Anton Orlov
 * Date: 05.03.2018
 * Time: 10:58
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Item;

/**
 * Class CartItem
 * @package FMT\InfrastructureBundle\Service\Nebook\RestApi\Item
 *
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class CartItem
{
    const PURCHASE_TYPE_RESERVATION = "Reservation";
    const PURCHASE_TYPE_PURCHASE = "Purchase";

    /** @var int */
    private $catalogId;

    /** @var int */
    private $groupId;

    /** @var int */
    private $sectionId;

    /** @var string */
    private $familyId;

    /** @var string */
    private $sku;

    /** @var int */
    private $quantity;

    /** @var int */
    private $price;

    /** @var bool */
    private $rental;

    /** @var bool */
    private $allowSubstitution;

    /** @var string[] */
    private $itemAttributes;

    /** @var string[string] */
    private $productAttributes;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var string */
    private $isbn;

    /** @var string */
    private $imageFile;

    /** @var string */
    private $imageThumbnail;

    /** @var bool */
    private $textbook;

    /** @var int */
    private $purchaseType;

    /**
     * @return int
     */
    public function getCatalogId()
    {
        return $this->catalogId;
    }

    /**
     * @param int $catalogId
     */
    public function setCatalogId($catalogId)
    {
        $this->catalogId = $catalogId;
    }

    /**
     * @return int
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @param int $groupId
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
    }

    /**
     * @return int
     */
    public function getSectionId()
    {
        return $this->sectionId;
    }

    /**
     * @param int $sectionId
     */
    public function setSectionId($sectionId)
    {
        $this->sectionId = $sectionId;
    }

    /**
     * @return string
     */
    public function getFamilyId()
    {
        return $this->familyId;
    }

    /**
     * @param string $familyId
     */
    public function setFamilyId($familyId)
    {
        $this->familyId = $familyId;
    }

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param int $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return boolean
     */
    public function isRental()
    {
        return $this->rental;
    }

    /**
     * @param boolean $rental
     */
    public function setRental($rental)
    {
        $this->rental = $rental;
    }

    /**
     * @return boolean
     */
    public function isAllowSubstitution()
    {
        return $this->allowSubstitution;
    }

    /**
     * @param boolean $allowSubstitution
     */
    public function setAllowSubstitution($allowSubstitution)
    {
        $this->allowSubstitution = $allowSubstitution;
    }

    /**
     * @return \string[]
     */
    public function getItemAttributes()
    {
        return $this->itemAttributes;
    }

    /**
     * @return string
     */
    public function getProductAttributes()
    {
        return $this->productAttributes;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
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
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * @return string
     */
    public function getImageThumbnail()
    {
        return $this->imageThumbnail;
    }

    /**
     * @return boolean
     */
    public function isTextbook()
    {
        return $this->textbook;
    }

    /**
     * @return int
     */
    public function getPurchaseType()
    {
        return $this->purchaseType;
    }
}
