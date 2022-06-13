<?php
/**
 * Author: Anton Orlov
 * Date: 27.04.2018
 * Time: 16:16
 */

namespace FMT\DomainBundle\Event;

use FMT\DataBundle\Entity\UserTransaction;
use Symfony\Component\EventDispatcher\Event;

class TransactionEvent extends Event
{
    const TRANSACTION_STARTED = "fmt.transaction_started";
    const TRANSACTION_COMPLETED = "fmt.transaction_completed";
    const TRANSACTION_FAILED = "fmt.transaction_failed";

    /** @var UserTransaction */
    private $transaction;

    public function __construct(UserTransaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * @return UserTransaction
     */
    public function getTransaction(): UserTransaction
    {
        return $this->transaction;
    }
}
