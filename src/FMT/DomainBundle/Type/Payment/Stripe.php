<?php
/**
 * Author: Anton Orlov
 * Date: 28.04.2018
 * Time: 16:13
 */

namespace FMT\DomainBundle\Type\Payment;

class Stripe implements ChargeCalculatorInterface
{
    /** @var float */
    private $commission;

    /** @var int */
    private $static;

    /**
     * @param float $commission
     * @param int $static
     */
    public function __construct(float $commission, int $static)
    {
        if ($commission >= 1) {
            throw new \RuntimeException("Stripe commission could not be greater or equal 1 (100%)");
        } elseif ($commission <= 0) {
            throw new \RuntimeException("Stripe commission could not be equal or lower than 0%");
        }

        if ($static < 0) {
            throw new \RuntimeException("Stripe static fee value could not be negative");
        }

        $this->commission = $commission;
        $this->static = $static;
    }

    /**
     * @return float
     */
    public function getCommission()
    {
        return $this->commission;
    }

    /**
     * @return int
     */
    public function getStatic()
    {
        return $this->static;
    }

    /**
     * Method calculates and returns value of the fee (in cents)
     *
     * @param int $cents
     * @return int
     */
    public function fee(int $cents)
    {
        return intval(ceil($cents / (1 - $this->commission)) - $cents) + $this->static;
    }

    /**
     * Method calculates and returns the value that should be charged by payment system to receive specified net
     *
     * @param int $netCents
     * @return int
     */
    public function charge(int $netCents)
    {
        return intval(ceil($netCents / (1 - $this->commission) +  $this->static));
    }
}
