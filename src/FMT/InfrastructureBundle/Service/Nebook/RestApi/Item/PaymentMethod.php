<?php
/**
 * Author: Anton Orlov
 * Date: 05.03.2018
 * Time: 12:41
 */

namespace FMT\InfrastructureBundle\Service\Nebook\RestApi\Item;

class PaymentMethod
{
    /** @var int */
    private $tenderId;

    /** @var string */
    private $backofficeId;

    /** @var string */
    private $tenderName;

    /** @var string */
    private $accountNumber;

    /** @var string */
    private $cardNumber;

    /** @var string */
    private $firstName;

    /** @var string */
    private $lastName;

    /** @var string */
    private $cardCode;

    /** @var \DateTime */
    private $expirationDate;

    /** @var int */
    private $amount;

    /**
     * @return int
     */
    public function getTenderId()
    {
        return $this->tenderId;
    }

    /**
     * @param int $tenderId
     * @return $this
     */
    public function setTenderId($tenderId)
    {
        $this->tenderId = $tenderId;
        return $this;
    }

    /**
     * @return string
     */
    public function getBackofficeId()
    {
        return $this->backofficeId;
    }

    /**
     * @return string
     */
    public function getTenderName()
    {
        return $this->tenderName;
    }

    /**
     * @return string
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * @param string $accountNumber
     * @return $this
     */
    public function setAccountNumber($accountNumber)
    {
        $this->accountNumber = $accountNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    /**
     * @param string $cardNumber
     * @return $this
     */
    public function setCardNumber($cardNumber)
    {
        $this->cardNumber = $cardNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getCardCode()
    {
        return $this->cardCode;
    }

    /**
     * @param string $cardCode
     * @return $this
     */
    public function setCardCode($cardCode)
    {
        $this->cardCode = $cardCode;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * @param \DateTime $expirationDate
     * @return $this
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;
        return $this;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }
}
