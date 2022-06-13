<?php

namespace FMT\InfrastructureBundle\Service\Payments\Stripe;

use Stripe\Exception\ApiErrorException;
use Stripe\Invoice;

/**
 * Trait InvoiceTrait
 * @package FMT\InfrastructureBundle\Service\Payments\Stripe
 */
trait InvoiceTrait
{
    /**
     * @param string $id
     * @return Invoice
     * @throws ApiErrorException
     */
    public function getInvoice(string $id): Invoice
    {
        return Invoice::retrieve($id);
    }
}
