<?php

namespace SocialDept\AtpResolver\Cache;

use Illuminate\Contracts\Cache\Repository;
use SocialDept\AtpResolver\Contracts\CacheStore;

class LaravelCacheStore implements CacheStore
{
    protected string $prefix = 'beacon:';

    /**
     * Create a new Laravel cache store instance.
     */
    public function __construct(
        protected Repository $cache
    ) {
    }

    /**
     * Get a cached value.
     *
     * @param  string  $key
     */
    public function get(string $key): mixed
    {
        return $this->cache->get($this->prefix.$key);
    }

    /**
     * Store a value in the cache.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  int  $ttl  Time to live in seconds
     */
    public function put(string $key, mixed $value, int $ttl): void
    {
        $this->cache->put($this->prefix.$key, $value, $ttl);
    }

    /**
     * Check if a key exists in the cache.
     *
     * @param  string  $key
     */
    public function has(string $key): bool
    {
        return $this->cache->has($this->prefix.$key);
    }

    /**
     * Remove a value from the cache.
     *
     * @param  string  $key
     */
    public function forget(string $key): void
    {
        $this->cache->forget($this->prefix.$key);
    }

    /**
     * Clear all cached values.
     */
    public function flush(): void
    {
        $this->cache->flush();
    }
}
