<?php
/**
 * Author: Anton Orlov
 * Date: 05.03.2018
 * Time: 16:56
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Item;

class OrderPaymentMethod
{
    /** @var string */
    private $accountInfo;

    /** @var int */
    private $amount;

    /** @var bool */
    private $creditCard;

    /** @var string */
    private $name;

    /**
     * @return string
     */
    public function getAccountInfo()
    {
        return $this->accountInfo;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return boolean
     */
    public function isCreditCard()
    {
        return $this->creditCard;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
