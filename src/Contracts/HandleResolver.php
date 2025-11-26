<?php

namespace SocialDept\AtpResolver\Contracts;

interface HandleResolver
{
    /**
     * Resolve a handle to a DID.
     *
     * @param  string  $handle  The handle to resolve (e.g., "user.bsky.social")
     * @return string The resolved DID
     *
     * @throws \SocialDept\AtpResolver\Exceptions\HandleResolutionException
     */
    public function resolve(string $handle): string;
}
