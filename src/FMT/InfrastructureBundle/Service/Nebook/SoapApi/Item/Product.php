<?php
/**
 * Author: Vladimir Bykovsky
 * Date: 11.11.2021
 * Time: 15:10
 */

namespace FMT\InfrastructureBundle\Service\Nebook\SoapApi\Item;

/**
 * Class Product
 */
class Product
{
    const TYPE_GM = "GM";
    const TYPE_TEXT = "Text";
    const TYPE_TRADE = "Trade";

    /** @var string */
    private $id;

    /** @var string */
    private $type;

    /** @var string */
    private $sku;

    /** @var string */
    private $title;

    /** @var string */
    private $isbn;

    /** @var string */
    private $newUsed;

    /** @var string */
    private $qty;

    /** @var string */
    private $price;

    /** @var string */
    private $acctCost;

    /** @var string */
    private $guide;

    /** @var string */
    private $guideRetail;

    /** @var string */
    private $adopted;

    /** @var string */
    private $term;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
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
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getIsbn()
    {
        return $this->isbn;
    }

    /**
     * @param string $isbn
     */
    public function setIsbn($isbn)
    {
        $this->isbn = $isbn;
    }

    /**
     * @return string
     */
    public function getNewUsed()
    {
        return $this->newUsed;
    }

    /**
     * @param string $newUsed
     */
    public function setNewUsed($newUsed)
    {
        $this->newUsed = $newUsed;
    }

    /**
     * @return string
     */
    public function getQty()
    {
        return $this->qty;
    }

    /**
     * @param string $qty
     */
    public function setQty($qty)
    {
        $this->qty = $qty;
    }

    /**
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param string $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return string
     */
    public function getAcctCost()
    {
        return $this->acctCost;
    }

    /**
     * @param string $acctCost
     */
    public function setAcctCost($acctCost)
    {
        $this->acctCost = $acctCost;
    }

    /**
     * @return string
     */
    public function getGuide()
    {
        return $this->guide;
    }

    /**
     * @param string $guide
     */
    public function setGuide($guide)
    {
        $this->guide = $guide;
    }

    /**
     * @return string
     */
    public function getGuideRetail()
    {
        return $this->guideRetail;
    }

    /**
     * @param string $guideRetail
     */
    public function setGuideRetail($guideRetail)
    {
        $this->guideRetail = $guideRetail;
    }

    /**
     * @return string
     */
    public function getAdopted()
    {
        return $this->adopted;
    }

    /**
     * @param string $adopted
     */
    public function setAdopted($adopted)
    {
        $this->adopted = $adopted;
    }

    /**
     * @return string
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * @param string $term
     */
    public function setTerm($term)
    {
        $this->term = $term;
    }
}
