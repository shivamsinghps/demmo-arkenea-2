<?php
/**
 * Author: Anton Orlov
 * Date: 28.04.2018
 * Time: 16:32
 */

namespace FMT\DomainBundle\Type\Payment;

interface CommissionInterface
{
    /**
     * Method calculates and returns value of the fee (in cents)
     *
     * @param int $cents
     * @return int
     */
    public function fee(int $cents);

    /**
     * Method calculates and returns the value that should be charged by payment system to receive specified net
     *
     * @param int $netCents
     * @return int
     */
    public function charge(int $netCents);
}
