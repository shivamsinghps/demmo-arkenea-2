<?php
/**
 * Author: Anton Orlov
 * Date: 26.03.2018
 * Time: 19:06
 */

namespace FMT\InfrastructureBundle\Helper;

use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class CacheHelper
{
    /** @var AdapterInterface */
    private static $adapter;

    /** @var string */
    private static $prefix;

    /**
     * @param AdapterInterface $adapter
     * @param string $prefix
     */
    public static function init(AdapterInterface $adapter, $prefix = null)
    {
        if (empty(self::$adapter)) {
            self::$adapter = $adapter;
            self::$prefix = is_null($prefix) ? null : (string) $prefix;
        }
    }

    /**
     * Methods looks up cache entity in cache and returns value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        if (empty(self::$adapter)) {
            return $default;
        }

        $result = self::$adapter->getItem(self::buildKey($key));

        if ($result->isHit()) {
            return $result->get();
        }

        return $default;
    }

    /**
     * Method looks up cache entity inside cache and invokes callable when key is not exists to set a value
     *
     * @param string $key
     * @param \Closure $callable
     * @param int $timeout
     * @return mixed
     */
    public static function cache($key, $callable, $timeout = null)
    {
        if (empty(self::$adapter)) {
            return call_user_func($callable, null);
        }

        $result = self::$adapter->getItem(self::buildKey($key));

        if (!$result->isHit()) {
            self::save($result, call_user_func($callable, $result), $timeout);
        }

        return $result->get();
    }

    /**
     * Method trying to add new item into the cache and fail when item already exists
     *
     * @param string $key
     * @param mixed $value
     * @param int $timeout
     * @return bool
     */
    public static function add($key, $value, $timeout = null)
    {
        if (empty(self::$adapter)) {
            return false;
        }

        $item = self::$adapter->getItem(self::buildKey($key));

        if ($item->isHit()) {
            return false;
        }

        return self::save($item, $value, $timeout);
    }

    /**
     * Method sets (add or update) item in the cache
     *
     * @param string $key
     * @param mixed $value
     * @param int $timeout
     * @return bool
     */
    public static function set($key, $value, $timeout = null)
    {
        if (empty(self::$adapter)) {
            return false;
        }

        return self::save(self::$adapter->getItem(self::buildKey($key)), $value, $timeout);
    }

    /**
     * Method trying to update existing item in the case and fail when item is not exist
     *
     * @param $key
     * @param $value
     * @param null $timeout
     * @return bool
     */
    public static function update($key, $value, $timeout = null)
    {
        if (empty(self::$adapter)) {
            return false;
        }

        $item = self::$adapter->getItem(self::buildKey($key));

        if (!$item->isHit()) {
            return false;
        }

        return self::save($item, $value, $timeout);
    }

    /**
     * Method deletes item from cache
     *
     * @param string $key
     * @return bool
     */
    public static function delete($key)
    {
        if (empty(self::$adapter)) {
            return false;
        }

        return self::$adapter->deleteItem(self::buildKey($key));
    }

    /**
     * @param string $key
     * @return string
     */
    private static function buildKey($key)
    {
        $result = $key;
        if (!is_null(self::$prefix)) {
            $result = sprintf("%s%s", self::$prefix, $key);
        }
        return $result;
    }

    /**
     * @param CacheItemInterface $item
     * @param mixed $value
     * @param int $timeout
     * @return bool
     */
    private static function save(CacheItemInterface $item, $value, $timeout)
    {
        $item->set($value);

        if (!is_null($timeout)) {
            $item->expiresAfter($timeout);
        }

        return self::$adapter->save($item);
    }
}
