<?php
/**
 * Author: Anton Orlov
 * Date: 28.02.2018
 * Time: 17:19
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Item;

class Material
{
    /** @var string */
    private $backofficeId;

    /** @var int */
    private $actualBuyback;

    /** @var int */
    private $estimatedBuyback;

    /** @var bool */
    private $isNewOnly;

    /** @var bool */
    private $isRentOnly;

    /** @var int */
    private $quantity;

    /** @var int */
    private $shortOrder;

    /** @var ProductFamily */
    private $family;

    /** @var Requirement */
    private $requirement;

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
    public function getActualBuyback()
    {
        return $this->actualBuyback;
    }

    /**
     * @return int
     */
    public function getEstimatedBuyback()
    {
        return $this->estimatedBuyback;
    }

    /**
     * @return boolean
     */
    public function isNewOnly()
    {
        return $this->isNewOnly;
    }

    /**
     * @return boolean
     */
    public function isRentOnly()
    {
        return $this->isRentOnly;
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
    public function getShortOrder()
    {
        return $this->shortOrder;
    }

    /**
     * @return ProductFamily
     */
    public function getFamily()
    {
        return $this->family;
    }

    /**
     * @return Requirement
     */
    public function getRequirement()
    {
        return $this->requirement;
    }
}
