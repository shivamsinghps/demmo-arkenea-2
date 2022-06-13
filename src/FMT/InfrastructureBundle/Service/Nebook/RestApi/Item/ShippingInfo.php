<?php
/**
 * Author: Anton Orlov
 * Date: 05.03.2018
 * Time: 12:46
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Item;

class ShippingInfo
{
    /** @var int */
    private $shippingCodeId;

    /** @var string */
    private $backofficeId;

    /** @var string */
    private $shippingName;

    /** @var string */
    private $instructions;

    /** @var Address */
    private $address;

    /**
     * @param int $shippingCodeId
     */
    public function setShippingCodeId(int $shippingCodeId)
    {
        $this->shippingCodeId = $shippingCodeId;
    }

    /**
     * @return int
     */
    public function getShippingCodeId(): int
    {
        return $this->shippingCodeId;
    }

    /**
     * @return string
     */
    public function getBackofficeId(): string
    {
        return $this->backofficeId;
    }

    /**
     * @return string
     */
    public function getShippingName(): string
    {
        return $this->shippingName;
    }

    /**
     * @param string $instructions
     */
    public function setInstructions(string $instructions)
    {
        $this->instructions = $instructions;
    }

    /**
     * @return string
     */
    public function getInstructions(): string
    {
        return $this->instructions;
    }

    /**
     * @param Address $address
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;
    }

    /**
     * @return Address
     */
    public function getAddress(): Address
    {
        return $this->address;
    }
}
