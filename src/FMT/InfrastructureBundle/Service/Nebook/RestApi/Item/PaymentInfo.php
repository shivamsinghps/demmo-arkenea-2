<?php
/**
 * Author: Anton Orlov
 * Date: 05.03.2018
 * Time: 12:40
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Item;

class PaymentInfo
{
    /** @var Address */
    private $address;

    /** @var PaymentMethod[] */
    private $methods = [];

    /**
     * @param Address|null $address
     * @return $this
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return Address|null
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param PaymentMethod $method
     * @return $this
     */
    public function addMethod(PaymentMethod $method)
    {
        $this->methods[] = $method;
        return $this;
    }

    /**
     * @param PaymentMethod[] $methods
     * @return $this
     */
    public function setMethods($methods)
    {
        $this->methods = $methods;
        return $this;
    }

    /**
     * @return PaymentMethod
     */
    public function getFirstMethod()
    {
        return count($this->methods) > 0 ? $this->methods[0] : null;
    }

    /**
     * @return PaymentMethod[]
     */
    public function getMethods()
    {
        return $this->methods;
    }
}
