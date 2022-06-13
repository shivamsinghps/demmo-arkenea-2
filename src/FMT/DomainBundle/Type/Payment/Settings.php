<?php
/**
 * Author: Anton Orlov
 * Date: 28.04.2018
 * Time: 16:27
 */

namespace FMT\DomainBundle\Type\Payment;

class Settings
{
    /** @var CommissionInterface */
    public $application;

    /** @var CommissionInterface */
    public $paymentService;

    /** @var string */
    public $currency;

    /** @var string */
    public $publicKey;

    /** @var bool */
    public $live;
}
