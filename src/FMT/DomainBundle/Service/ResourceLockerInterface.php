<?php

namespace FMT\DomainBundle\Service;

/**
 * Interface ResourceLockerInterface
 * @package FMT\DomainBundle\Service
 */
interface ResourceLockerInterface
{
    /**
     * @return bool
     */
    public function lock();

    public function release();

    /**
     * @return bool
     */
    public function isLocked();
}
