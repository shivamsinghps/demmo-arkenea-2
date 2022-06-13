<?php
/**
 * Author: Anton Orlov
 * Date: 02.04.2018
 * Time: 17:47
 */

namespace FMT\InfrastructureBundle\Service\AmazonS3;

use Aws\CacheInterface;
use FMT\InfrastructureBundle\Helper\CacheHelper;

class DefaultCacheAdapter implements CacheInterface
{
    /**
     * Get a cache item by key.
     *
     * @param string $key Key to retrieve.
     *
     * @return mixed|null Returns the value or null if not found.
     */
    public function get($key)
    {
        return CacheHelper::get($key);
    }

    /**
     * Set a cache key value.
     *
     * @param string $key Key to set
     * @param mixed $value Value to set.
     * @param int $ttl Number of seconds the item is allowed to live. Set
     *                      to 0 to allow an unlimited lifetime.
     */
    public function set($key, $value, $ttl = 0)
    {
        CacheHelper::set($key, $value, $ttl == 0 ? null : $ttl);
    }

    /**
     * Remove a cache key.
     *
     * @param string $key Key to remove.
     */
    public function remove($key)
    {
        CacheHelper::delete($key);
    }
}
