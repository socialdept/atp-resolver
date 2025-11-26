<?php

namespace SocialDept\AtpResolver\Contracts;

use SocialDept\AtpResolver\Data\DidDocument;

interface DidResolver
{
    /**
     * Resolve a DID to a DID Document.
     *
     * @param  string  $did  The DID to resolve (e.g., "did:plc:abc123" or "did:web:example.com")
     * @return DidDocument
     *
     * @throws \SocialDept\AtpResolver\Exceptions\DidResolutionException
     */
    public function resolve(string $did): DidDocument;

    /**
     * Check if this resolver supports the given DID method.
     *
     * @param  string  $method  The DID method (e.g., "plc", "web")
     */
    public function supports(string $method): bool;
}
