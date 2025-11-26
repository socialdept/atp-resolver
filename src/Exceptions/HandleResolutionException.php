<?php

namespace SocialDept\AtpResolver\Exceptions;

class HandleResolutionException extends ResolverException
{
    /**
     * Create a new exception for invalid handle format.
     *
     * @param  string  $handle
     */
    public static function invalidFormat(string $handle): self
    {
        return new self("Invalid handle format: {$handle}");
    }

    /**
     * Create a new exception for resolution failure.
     *
     * @param  string  $handle
     * @param  string  $reason
     */
    public static function resolutionFailed(string $handle, string $reason = ''): self
    {
        $message = "Failed to resolve handle: {$handle}";

        if ($reason) {
            $message .= " ({$reason})";
        }

        return new self($message);
    }
}
