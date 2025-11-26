<?php

namespace SocialDept\AtpResolver\Support\Concerns;

trait HasConfig
{
    /**
     * Get configuration value with fallback for testing.
     */
    protected function getConfig(string $key, mixed $default): mixed
    {
        if (! function_exists('config')) {
            return $default;
        }

        try {
            return config($key, $default);
        } catch (\Throwable) {
            return $default;
        }
    }
}
