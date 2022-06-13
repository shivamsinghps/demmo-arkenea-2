<?php
/**
 * Author: Vladimir Bykovsky
 * Date: 11.11.2021
 * Time: 15:10
 */

namespace FMT\InfrastructureBundle\Service\Nebook\SoapApi\Item;

/**
 * Class TaxMethod
 */
class TaxMethod
{
    /** @var int */
    private $id;

    /** @var string */
    private $state;

    /** @var int */
    private $amount;

    /** @var bool */
    private $shipInd;

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
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return bool
     */
    public function getShipInd()
    {
        return $this->shipInd;
    }
}
