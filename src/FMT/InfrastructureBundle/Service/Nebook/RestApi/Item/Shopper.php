<?php
/**
 * Author: Anton Orlov
 * Date: 03.03.2018
 * Time: 16:55
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Item;

/**
 * Class Shopper
 * @package FMT\InfrastructureBundle\Service\Nebook\RestApi\Item
 *
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Shopper
{
    /** @var string */
    private $id;

    /** @var int */
    private $shopperNumber;

    /** @var string */
    private $studentId;

    /** @var string */
    private $membershipId;

    /** @var \DateTime */
    private $created;

    /** @var string */
    private $email;

    /** @var string */
    private $password;

    /** @var bool */
    private $isDisabled;

    /** @var bool */
    private $isTaxExempt;

    /** @var bool */
    private $allowBuybackEmail;

    /** @var bool */
    private $allowDirectEmail;

    /** @var Address */
    private $billingAddress;

    /** @var Address */
    private $shippingAddress;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getShopperNumber()
    {
        return $this->shopperNumber;
    }

    /**
     * @return string
     */
    public function getStudentId()
    {
        return $this->studentId;
    }

    /**
     * @param string $studentId
     */
    public function setStudentId($studentId)
    {
        $this->studentId = $studentId;
    }

    /**
     * @return string
     */
    public function getMembershipId()
    {
        return $this->membershipId;
    }

    /**
     * @param string $membershipId
     */
    public function setMembershipId($membershipId)
    {
        $this->membershipId = $membershipId;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return boolean
     */
    public function isDisabled()
    {
        return $this->isDisabled;
    }

    /**
     * @param boolean $isDisabled
     */
    public function setDisabled($isDisabled)
    {
        $this->isDisabled = $isDisabled;
    }

    /**
     * @return boolean
     */
    public function isTaxExempt()
    {
        return $this->isTaxExempt;
    }

    /**
     * @param boolean $isTaxExempt
     */
    public function setTaxExempt($isTaxExempt)
    {
        $this->isTaxExempt = $isTaxExempt;
    }

    /**
     * @return boolean
     */
    public function isAllowBuybackEmail()
    {
        return $this->allowBuybackEmail;
    }

    /**
     * @param boolean $allowBuybackEmail
     */
    public function setAllowBuybackEmail($allowBuybackEmail)
    {
        $this->allowBuybackEmail = $allowBuybackEmail;
    }

    /**
     * @return boolean
     */
    public function isAllowDirectEmail()
    {
        return $this->allowDirectEmail;
    }

    /**
     * @param boolean $allowDirectEmail
     */
    public function setAllowDirectEmail($allowDirectEmail)
    {
        $this->allowDirectEmail = $allowDirectEmail;
    }

    /**
     * @return Address
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * @param Address $billingAddress
     */
    public function setBillingAddress($billingAddress)
    {
        $this->billingAddress = $billingAddress;
    }

    /**
     * @return Address
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * @param Address $shippingAddress
     */
    public function setShippingAddress($shippingAddress)
    {
        $this->shippingAddress = $shippingAddress;
    }
}
