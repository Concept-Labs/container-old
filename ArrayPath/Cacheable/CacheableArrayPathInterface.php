<?php
namespace Cl\Container\ArrayPath\Cacheable;

use Cl\Cache\CacheItemPoolInterface;
use Cl\Container\ArrayPath\ArrayPathInterface;

/**
 * Cacheable ArrayPath interface
 */
interface CacheableArrayPathInterface extends ArrayPathInterface
{

    /**
     * Get cache item pool
     *
     * @return CacheItemPoolInterface
     */
    public function getCacheItemPool() : CacheItemPoolInterface;

    /**
     * Get cache item pool
     * 
     * @param CacheItemPoolInterface $cacheItemPool 
     *
     * @return CacheItemPoolInterface
     */
    public function setCacheItemPool(CacheItemPoolInterface $cacheItemPool) : CacheableArrayPathInterface;

    /**
     * Get the cache key.
     * 
     * @param string $key 
     * 
     * @return string
     */
    public function getCacheKey(string $key): string;

    /**
     * Get item from cache
     *
     * @param string $key 
     * 
     * @return mixed|null
     */
    public function offsetCacheGet(string $key) : mixed;

    /**
     * Save value to cache
     *
     * @param string $key 
     * @param mixed  $value 
     * 
     * @return bool
     */
    public function offsetCacheSave(string $key, mixed $value): bool;

    /**
     * Delete the cache item for the specified key
     *
     * @param string $key 
     * 
     * @return bool
     */
    public function offsetCacheDelete(string $key): bool;

    /**
     * Clear the cache
     *
     * @return bool
     */
    public function cacheClear(): bool;
}