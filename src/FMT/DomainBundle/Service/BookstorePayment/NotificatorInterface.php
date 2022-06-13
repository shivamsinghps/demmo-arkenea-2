<?php

declare(strict_types=1);

namespace FMT\DomainBundle\Service\BookstorePayment;

/**
 * Interface NotificatorInterface
 */
interface NotificatorInterface
{
    /**
     * @param SuccessTransfer $successTransfer
     */
    public function transferSend(SuccessTransfer $successTransfer): void;
}
