<?php
/**
 * Author: Anton Orlov
 * Date: 04.05.2018
 * Time: 10:42
 */

namespace FMT\DomainBundle\Service\PaymentProcessor;

use FMT\DataBundle\Entity\UserTransaction;
use FMT\DomainBundle\Exception\PaymentException;
use Stripe\Exception\ApiErrorException;

/**
 * Interface ProcessorInterface
 * @package FMT\DomainBundle\Service\PaymentProcessor
 */
interface ProcessorInterface
{
    /**
     * Method checks if current processor supports provided descriptor
     *
     * @param string $descriptor
     * @return bool
     */
    public function isSupport(string $descriptor);

    /**
     * Method charges client using implementation of corresponding payment processor
     *
     * @param UserTransaction $transaction
     * @return string
     * @throws PaymentException
     */
    public function charge(UserTransaction $transaction);

    /**
     * @param string $txnId
     * @return \Stripe\Charge|null
     * @throws ApiErrorException
     */
    public function getCharge(string $txnId);
}
