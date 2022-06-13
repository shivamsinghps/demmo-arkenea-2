<?php
/**
 * Author: Vladimir Bykovsky
 * Date: 11.11.2021
 * Time: 15:10
 */

namespace FMT\InfrastructureBundle\Service\Nebook\SoapApi\Item;

/**
 * Class ShippingCode
 */
class ShippingCode
{
    /** @var int */
    private $id;

    /** @var int */
    private $backofficeId;

    /** @var string */
    private $type;

    /** @var string */
    private $method;

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
     * @return int
     */
    public function getBackofficeId()
    {
        return $this->backofficeId;
    }

    /**
     * @param int $backofficeId
     */
    public function setBackofficeId($backofficeId)
    {
        $this->backofficeId = $backofficeId;
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
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }
}
