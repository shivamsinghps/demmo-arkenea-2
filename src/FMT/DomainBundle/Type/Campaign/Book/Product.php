<?php

namespace FMT\DomainBundle\Type\Campaign\Book;

use FMT\InfrastructureBundle\Helper\CurrencyHelper;

class Product
{
    /** @var string */
    private $familyId;

    /** @var string */
    private $name;

    /** @var string */
    private $author;

    /** @var string */
    private $isbn;

    /** @var string */
    private $sku;

    /** @var string */
    private $state;

    /** @var int */
    private $price;

    /** @var int */
    private $calculatedInventory;

    /**
     * @return string
     */
    public function getFamilyId()
    {
        return $this->familyId;
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
    public function getAuthor()
    {
        return $this->author;
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
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
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
    public function getConvertedPrice()
    {
        return CurrencyHelper::priceFilter($this->price);
    }

    /**
     * @return int
     */
    public function getCalculatedInventory()
    {
        return $this->calculatedInventory;
    }
}
