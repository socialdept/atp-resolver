<?php

namespace SocialDept\AtpResolver\Exceptions;

class DidResolutionException extends ResolverException
{
    /**
     * Create a new exception for unsupported DID method.
     *
     * @param  string  $method
     */
    public static function unsupportedMethod(string $method): self
    {
        return new self("Unsupported DID method: {$method}");
    }

    /**
     * Create a new exception for invalid DID format.
     *
     * @param  string  $did
     */
    public static function invalidFormat(string $did): self
    {
        return new self("Invalid DID format: {$did}");
    }

    /**
     * Create a new exception for resolution failure.
     *
     * @param  string  $did
     * @param  string  $reason
     */
    public static function resolutionFailed(string $did, string $reason = ''): self
    {
        $message = "Failed to resolve DID: {$did}";

        if ($reason) {
            $message .= " ({$reason})";
        }

        return new self($message);
    }
}
