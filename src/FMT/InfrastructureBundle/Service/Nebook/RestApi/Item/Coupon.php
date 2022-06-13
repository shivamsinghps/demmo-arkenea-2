<?php
/**
 * Author: Anton Orlov
 * Date: 05.03.2018
 * Time: 12:38
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Item;

class Coupon
{
    /**
     * TODO: Define constants of scope attribute
     */

    /** @var string */
    private $code;

    /** @var string */
    private $name;

    /** @var int */
    private $scope;

    /** @var int */
    private $amount;

    /** @var float */
    private $discount;

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return float
     */
    public function getDiscount()
    {
        return $this->discount;
    }
}
