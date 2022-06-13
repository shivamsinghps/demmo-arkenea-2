<?php
/**
 * Author: Anton Orlov
 * Date: 28.04.2018
 * Time: 16:19
 */

namespace FMT\DomainBundle\Type\Payment;

class FMT implements ChargeCalculatorInterface
{
    /** @var float */
    private $commission;

    public function __construct(float $commission)
    {
        if ($commission >= 1) {
            throw new \RuntimeException("FMT commission could not be greater or equal 1 (100%)");
        } elseif ($commission < 0) {
            throw new \RuntimeException("FMT commission could not be lower than 0%");
        }

        $this->commission = $commission;
    }

    /**
     * @return float
     */
    public function getCommission()
    {
        return $this->commission;
    }

    /**
     * Method calculates and returns value of the fee (in cents)
     *
     * @param int $cents
     * @return int
     */
    public function fee(int $cents)
    {
        return intval(ceil($cents * $this->commission));
    }

    /**
     * Method calculates and returns the value that should be charged by payment system to receive specified net
     *
     * @param int $netCents
     * @return int
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function charge(int $netCents)
    {
        throw new \RuntimeException("Method is not implemented");
    }
}
