<?php
/**
 * Author: Anton Orlov
 * Date: 27.03.2018
 * Time: 15:20
 */

namespace FMT\InfrastructureBundle\Helper;

use Symfony\Component\Lock\Exception\LockConflictedException;
use Symfony\Component\Lock\Factory;

class LockHelper
{
    /** @var Factory */
    private static $locker;

    /**
     * @param Factory $locker
     */
    public static function init(Factory $locker)
    {
        if (empty(self::$locker)) {
            self::$locker = $locker;
        }
    }

    /**
     * Method tries to execute callable with lock
     *
     * @param string $name
     * @param \Closure $callable
     * @param int $timeout
     */
    public static function lock($name, $callable, $timeout = null)
    {
        if (empty(self::$locker)) {
            call_user_func($callable);
            return;
        }

        $lock = self::$locker->createLock($name, $timeout);
        try {
            if ($lock->acquire()) {
                call_user_func($callable);
            } else {
                throw new LockConflictedException(
                    sprintf("Could not lock resource `%s` - lock already acquired by another process", $name)
                );
            }
        } finally {
            $lock->release();
        }
    }
}
