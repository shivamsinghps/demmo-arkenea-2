<?php
/**
 * Author: Anton Orlov
 * Date: 23.04.2018
 * Time: 13:10
 */

namespace FMT\DomainBundle\Type\Payment;

use FMT\DataBundle\Entity\MinimalUserInterface;
use FMT\DataBundle\Entity\UnregisteredUserDto;
use FMT\DataBundle\Entity\User;
use FMT\DomainBundle\Service\PaymentProcessor\ProcessorInterface;

class Donation
{
    /** @var User */
    private $student;

    /** @var MinimalUserInterface */
    private $donor;

    /** @var bool */
    private $anonymous = true;

    /** @var float */
    private $paymentAmount;

    /** @var ProcessorInterface */
    private $paymentProcessor;

    public function __construct(?User $student)
    {
        $this->student = $student;
    }

    /**
     * @return User
     */
    public function getStudent()
    {
        return $this->student;
    }

    /**
     * @param User $student
     * @return $this
     */
    public function setStudent(User $student)
    {
        $this->student = $student;

        return $this;
    }

    /**
     * @return MinimalUserInterface
     */
    public function getDonor()
    {
        return $this->donor;
    }

    /**
     * @param User $donor
     * @return $this
     */
    public function setDonor(User $donor = null)
    {
        $this->donor = $donor;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->donor ? $this->donor->getEmail(): null;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->createDonor()->setEmail($email);

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->donor ? $this->donor->getFirstName() : null;
    }

    /**
     * @param string $firstName
     * @return $this
     */
    public function setFirstName($firstName)
    {
        $this->createDonor()->setFirstName($firstName);

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->donor ? $this->donor->getLastName() : null;
    }

    /**
     * @param string $lastName
     * @return $this
     */
    public function setLastName($lastName)
    {
        $this->createDonor()->setLastName($lastName);

        return $this;
    }

    /**
     * @return boolean
     */
    public function isAnonymous()
    {
        return $this->anonymous;
    }

    /**
     * @param boolean $anonymous
     */
    public function setAnonymous(bool $anonymous)
    {
        $this->anonymous = $anonymous;
    }

    /**
     * @return float
     */
    public function getPaymentAmount()
    {
        return $this->paymentAmount;
    }

    /**
     * @return int
     */
    public function getPaymentAmountCents()
    {
        return (int)ceil($this->paymentAmount * 100);
    }

    /**
     * @param float $paymentAmount
     */
    public function setPaymentAmount(float $paymentAmount)
    {
        $this->paymentAmount = $paymentAmount;
    }

    /**
     * @return ProcessorInterface
     */
    public function getPaymentProcessor()
    {
        return $this->paymentProcessor;
    }

    /**
     * @param ProcessorInterface $paymentProcessor
     */
    public function setPaymentProcessor(ProcessorInterface $paymentProcessor)
    {
        $this->paymentProcessor = $paymentProcessor;
    }

    /**
     * @return MinimalUserInterface
     */
    private function createDonor()
    {
        if (!$this->donor) {
            $this->donor = new UnregisteredUserDto();
        }

        return $this->donor;
    }
}
