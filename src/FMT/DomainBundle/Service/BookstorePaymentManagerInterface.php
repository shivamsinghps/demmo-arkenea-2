<?php

declare(strict_types=1);

namespace FMT\DomainBundle\Service;

use FMT\DomainBundle\Service\BookstorePayment\SendTime;

/**
 * Interface BookstorePaymentManagerInterface
 */
interface BookstorePaymentManagerInterface
{
    /**
     * @param SendTime|null $sendTime
     * @param bool|null $validTime
     * @return bool
     */
    public function sendTransfer(SendTime $sendTime, ?bool $validTime = true): bool;
}
