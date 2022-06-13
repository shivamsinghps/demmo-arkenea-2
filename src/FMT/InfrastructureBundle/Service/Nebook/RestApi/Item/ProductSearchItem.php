<?php
/**
 * Author: Anton Orlov
 * Date: 01.03.2018
 * Time: 10:57
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Item;

class ProductSearchItem
{
    /** @var string */
    private $id;

    /** @var int */
    private $type;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var string */
    private $imageThumbnail;

    /** @var int */
    private $quantity;

    /** @var int */
    private $minPrice;

    /** @var int */
    private $maxPrice;

    /** @var int */
    private $catalogId;

    /** @var string */
    private $catalogName;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
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

    /**
     * @return string
     */
    public function getImageThumbnail()
    {
        return $this->imageThumbnail;
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
    public function getMinPrice()
    {
        return $this->minPrice;
    }

    /**
     * @return int
     */
    public function getMaxPrice()
    {
        return $this->maxPrice;
    }

    /**
     * @return int
     */
    public function getCatalogId()
    {
        return $this->catalogId;
    }

    /**
     * @return string
     */
    public function getCatalogName()
    {
        return $this->catalogName;
    }
}
