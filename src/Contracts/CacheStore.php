<?php

namespace SocialDept\AtpResolver\Contracts;

interface CacheStore
{
    /**
     * Get a cached value.
     *
     * @param  string  $key
     * @return mixed
     */
    public function get(string $key): mixed;

    /**
     * Store a value in the cache.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  int  $ttl  Time to live in seconds
     */
    public function put(string $key, mixed $value, int $ttl): void;

    /**
     * Check if a key exists in the cache.
     *
     * @param  string  $key
     */
    public function has(string $key): bool;

    /**
     * Remove a value from the cache.
     *
     * @param  string  $key
     */
    public function forget(string $key): void;

    /**
     * Clear all cached values.
     */
    public function flush(): void;
}
