<?php
/**
 * Author: Anton Orlov
 * Date: 28.02.2018
 * Time: 17:36
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Item;

class ProductFamily
{
    const TYPE_UNKNOWN = "Unknown";
    const TYPE_GENERAL_MERCHANDISE = "GeneralMerchandise";
    const TYPE_TEXTBOOK = "Textbook";
    const TYPE_TRADEBOOK = "Tradebook";
    const TYPE_PACKAGE = "Package";
    const TYPE_TEXTREQ = "Textreq";
    const TYPE_EBOOK = "Ebook";
    const TYPE_BARCHARTS = "Barcharts";

    /** @var string */
    private $id;

    /** @var string */
    private $backofficeId;

    /** @var int */
    private $type;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var string */
    private $imageFileName;

    /** @var string */
    private $imageThumbnailName;

    /** @var bool */
    private $isReservable;

    /** @var int */
    private $inventoryTolerance;

    /** @var Product[] */
    private $products;

    /** @var BookInfo */
    private $info;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getBackofficeId()
    {
        return $this->backofficeId;
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

    /**: string
     * @return string
     */
    public function getImageFileName()
    {
        return $this->imageFileName;
    }

    /**
     * @return string
     */
    public function getImageThumbnailName()
    {
        return $this->imageThumbnailName;
    }

    /**
     * @return boolean
     */
    public function isReservable()
    {
        return $this->isReservable;
    }

    /**
     * @return int
     */
    public function getInventoryTolerance()
    {
        return $this->inventoryTolerance;
    }

    /**
     * @return Product[]
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @return BookInfo
     */
    public function getInfo()
    {
        return $this->info;
    }
}
