<?php

namespace SocialDept\AtpResolver\Support\Concerns;

use SocialDept\AtpResolver\Exceptions\DidResolutionException;

trait ParsesDid
{
    /**
     * Extract the method from a DID.
     */
    protected function extractMethod(string $did): string
    {
        if (! str_starts_with($did, 'did:')) {
            throw DidResolutionException::invalidFormat($did);
        }

        $parts = explode(':', $did);

        if (count($parts) < 3) {
            throw DidResolutionException::invalidFormat($did);
        }

        return $parts[1];
    }

    /**
     * Extract the identifier from a DID.
     */
    protected function extractIdentifier(string $did): string
    {
        $parts = explode(':', $did);

        if (count($parts) < 3) {
            throw DidResolutionException::invalidFormat($did);
        }

        return implode(':', array_slice($parts, 2));
    }
}
