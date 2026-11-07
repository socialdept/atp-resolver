# Usage

Part of the [atp-resolver docs](../introduction.md#documentation). Resolving DIDs, handles and PDS endpoints, caching, validation and errors.


## Resolving DIDs

Resolver supports both `did:plc` and `did:web` methods:

```php
use SocialDept\AtpResolver\Facades\Resolver;

// PLC directory resolution
$document = Resolver::resolveDid('did:plc:ewvi7nxzyoun6zhxrhs64oiz');

// Web DID resolution
$document = Resolver::resolveDid('did:web:example.com');

// Access document data
$handle = $document->getHandle();
$pdsEndpoint = $document->getPdsEndpoint();
$services = $document->service;
```

## Resolving Handles

Convert human-readable handles to DIDs or DID documents:

```php
// Convert handle to DID string
$did = Resolver::handleToDid('alice.bsky.social');
// "did:plc:..."

// Resolve handle to full DID document
$document = Resolver::resolveHandle('alice.bsky.social');
$handle = $document->getHandle();
$pds = $document->getPdsEndpoint();
```

## Resolving Identities

Automatically detect and resolve either DIDs or handles:

```php
// Works with DIDs
$document = Resolver::resolveIdentity('did:plc:ewvi7nxzyoun6zhxrhs64oiz');

// Works with handles
$document = Resolver::resolveIdentity('alice.bsky.social');

// Useful for user input that could be either
$actor = $request->input('actor'); // Could be DID or handle
$document = Resolver::resolveIdentity($actor);
```

## Finding PDS Endpoints

Automatically discover a user's Personal Data Server:

```php
// From a DID
$pds = Resolver::resolvePds('did:plc:ewvi7nxzyoun6zhxrhs64oiz');

// From a handle
$pds = Resolver::resolvePds('alice.bsky.social');

// Returns: "https://bsky.social" or user's custom PDS
```

This is particularly useful when you need to make API calls to a user's PDS instead of hardcoding Bluesky's public instance.

## Cache Management

The resolver caches resolutions. Clear the cache when needed:

```php
// Clear specific DID cache
Resolver::clearDidCache('did:plc:abc123');

// Clear specific handle cache
Resolver::clearHandleCache('alice.bsky.social');

// Clear specific PDS cache
Resolver::clearPdsCache('alice.bsky.social');

// Clear all cached data
Resolver::clearCache();
```

> [!WARNING]
> `clearCache()` flushes the whole default cache store, not only the resolver's entries.

The `cache.enabled` config key is not read, so caching cannot be turned off from config. Pass `useCache: false` instead.

## Disable Caching

Pass `false` as the second parameter to bypass cache:

```php
$document = Resolver::resolveDid('did:plc:abc123', useCache: false);
$did = Resolver::handleToDid('alice.bsky.social', useCache: false);
$document = Resolver::resolveIdentity('alice.bsky.social', useCache: false);
$pds = Resolver::resolvePds('alice.bsky.social', useCache: false);
```

## Identity Validation

The package includes static helper methods to validate DIDs and handles:

```php
use SocialDept\AtpResolver\Support\Identity;

// Validate handles
Identity::isHandle('alice.bsky.social'); // true
Identity::isHandle('invalid');           // false

// Validate DIDs
Identity::isDid('did:plc:ewvi7nxzyoun6zhxrhs64oiz'); // true
Identity::isDid('did:web:example.com');              // true
Identity::isDid('invalid');                          // false

// Extract DID method
Identity::extractDidMethod('did:plc:abc123'); // "plc"
Identity::extractDidMethod('did:web:test');   // "web"

// Check specific DID types
Identity::isPlcDid('did:plc:abc123'); // true
Identity::isWebDid('did:web:test');   // true
```

These helpers are useful for validating user input before making resolution calls.

## Configuration

Beacon works great with zero configuration, but you can customize behavior in `config/resolver.php`:

```php
return [
    // PLC directory for did:plc resolution
    'plc_directory' => env('RESOLVER_PLC_DIRECTORY', 'https://plc.directory'),

    // Default PDS endpoint for handle resolution
    'pds_endpoint' => env('RESOLVER_PDS_ENDPOINT', 'https://bsky.social'),

    // HTTP request timeout
    'timeout' => env('RESOLVER_TIMEOUT', 10),

    // Cache configuration
    'cache' => [
        'enabled' => env('RESOLVER_CACHE_ENABLED', true),

        // Cache TTL for DID documents (1 hour)
        'did_ttl' => env('RESOLVER_CACHE_DID_TTL', 3600),

        // Cache TTL for handle resolutions (1 hour)
        'handle_ttl' => env('RESOLVER_CACHE_HANDLE_TTL', 3600),

        // Cache TTL for PDS endpoints (1 hour)
        'pds_ttl' => env('RESOLVER_CACHE_PDS_TTL', 3600),
    ],
];
```

## API reference

## Available Methods

```php
// DID Resolution
Resolver::resolveDid(string $did, bool $useCache = true): DidDocument

// Handle Resolution
Resolver::handleToDid(string $handle, bool $useCache = true): string
Resolver::resolveHandle(string $handle, bool $useCache = true): DidDocument

// Identity Resolution
Resolver::resolveIdentity(string $actor, bool $useCache = true): DidDocument

// PDS Resolution
Resolver::resolvePds(string $actor, bool $useCache = true): ?string

// Cache Management
Resolver::clearDidCache(string $did): void
Resolver::clearHandleCache(string $handle): void
Resolver::clearPdsCache(string $actor): void
Resolver::clearCache(): void

// Identity Validation (static helpers)
Identity::isHandle(?string $handle): bool
Identity::isDid(?string $did): bool
Identity::extractDidMethod(string $did): ?string
Identity::isPlcDid(string $did): bool
Identity::isWebDid(string $did): bool
```

## DidDocument Object

```php
$document->id;                    // string - The DID
$document->alsoKnownAs;           // array - Alternative identifiers
$document->verificationMethod;    // array - Verification methods
$document->service;               // array - Service endpoints
$document->raw;                   // array - Raw DID document

// Helper methods
$document->getHandle();           // ?string - Extract handle from alsoKnownAs
$document->getPdsEndpoint();      // ?string - Extract PDS service endpoint
$document->toArray();             // array - Convert to array
```

## Errors

The resolver throws descriptive exceptions when resolution fails:

```php
use SocialDept\AtpResolver\Exceptions\DidResolutionException;
use SocialDept\AtpResolver\Exceptions\HandleResolutionException;

try {
    $document = Resolver::resolveDid('did:invalid:format');
} catch (DidResolutionException $e) {
    // Handle DID resolution errors
    logger()->error('DID resolution failed', [
        'message' => $e->getMessage(),
    ]);
}

try {
    $did = Resolver::handleToDid('invalid-handle');
} catch (HandleResolutionException $e) {
    // Handle handle resolution errors
}
```

