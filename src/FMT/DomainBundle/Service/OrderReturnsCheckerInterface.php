<?php

declare(strict_types=1);

namespace FMT\DomainBundle\Service;

/**
 * Interface OrderReturnsCheckerInterface
 * @package FMT\DomainBundle\Service
 */
interface OrderReturnsCheckerInterface
{
    /**
     * Check order returns
     */
    public function check(): void;
}
