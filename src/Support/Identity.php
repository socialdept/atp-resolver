<?php

namespace SocialDept\AtpResolver\Support;

class Identity
{
    // "***.bsky.social" "alice.test"
    protected const HANDLE_REGEX = '/^([a-zA-Z0-9]([a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]([a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?$/';

    // "did:plc:1234..." "did:web:alice.test"
    protected const DID_REGEX = '/^did:[a-z]+:[a-zA-Z0-9._:%-]*[a-zA-Z0-9._-]$/';

    /**
     * Check if a string is a valid handle.
     *
     * @param  string|null  $handle
     */
    public static function isHandle(?string $handle): bool
    {
        return preg_match(self::HANDLE_REGEX, $handle ?? '') === 1;
    }

    /**
     * Check if a string is a valid DID.
     *
     * @param  string|null  $did
     */
    public static function isDid(?string $did): bool
    {
        return preg_match(self::DID_REGEX, $did ?? '') === 1;
    }

    /**
     * Extract the DID method from a DID string.
     *
     * @param  string  $did
     * @return string|null Returns the method (e.g., "plc", "web") or null if invalid
     */
    public static function extractDidMethod(string $did): ?string
    {
        if (! self::isDid($did)) {
            return null;
        }

        $parts = explode(':', $did);

        return $parts[1] ?? null;
    }

    /**
     * Check if a DID uses the PLC method.
     *
     * @param  string  $did
     */
    public static function isPlcDid(string $did): bool
    {
        return self::extractDidMethod($did) === 'plc';
    }

    /**
     * Check if a DID uses the Web method.
     *
     * @param  string  $did
     */
    public static function isWebDid(string $did): bool
    {
        return self::extractDidMethod($did) === 'web';
    }
}
