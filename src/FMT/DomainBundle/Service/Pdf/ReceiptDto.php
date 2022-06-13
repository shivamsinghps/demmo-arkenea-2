<?php

namespace FMT\DomainBundle\Service\Pdf;

/**
 * Class ReceiptDto
 * @package FMT\DomainBundle\Service\Pdf
 */
class ReceiptDto
{
    public $number = '';
    public $date = null;
    public $amount = 0;
    public $paymentMethod = '';
}
