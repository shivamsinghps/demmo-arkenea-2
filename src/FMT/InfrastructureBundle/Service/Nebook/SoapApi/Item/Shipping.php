<?php
/**
 * Author: Vladimir Bykovsky
 * Date: 11.11.2021
 * Time: 15:10
 */

namespace FMT\InfrastructureBundle\Service\Nebook\SoapApi\Item;

/**
 * Class Shipping
 */
class Shipping
{
    /** @var int */
    private $id;

    /** @var string */
    private $amount;

    /** @var Address */
    private $address;

    /** @var string */
    private $instr;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param string $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param Address $address
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getInstr()
    {
        return $this->instr;
    }

    /**
     * @param string $instr
     */
    public function setInstr($instr)
    {
        $this->instr = $instr;
    }
}
