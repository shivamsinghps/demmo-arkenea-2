<?php

namespace FMT\DomainBundle\Type\Payment;

/**
 * Interface ChargeCalculatorInterface
 * @package FMT\DomainBundle\Type\Payment
 */
interface ChargeCalculatorInterface
{
    /**
     * @param int $cents
     * @return mixed
     */
    public function fee(int $cents);

    /**
     * Returns an amount to be charged so after fee is taken the rest is equal to the original amount
     *
     * @param int $netCents
     * @return mixed
     */
    public function charge(int $netCents);
}
