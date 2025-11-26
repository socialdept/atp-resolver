[![Resolver Header](./header.png)](https://github.com/socialdept/atp-signals)

<h3 align="center">
    Resolve AT Protocol identities in your Laravel application.
</h3>

<p align="center">
    <br>
    <a href="https://packagist.org/packages/socialdept/atp-resolver" title="Latest Version on Packagist"><img src="https://img.shields.io/packagist/v/socialdept/atp-resolver.svg?style=flat-square"></a>
    <a href="https://packagist.org/packages/socialdept/atp-resolver" title="Total Downloads"><img src="https://img.shields.io/packagist/dt/socialdept/atp-resolver.svg?style=flat-square"></a>
    <a href="https://github.com/socialdept/atp-resolver/actions/workflows/tests.yml" title="GitHub Tests Action Status"><img src="https://img.shields.io/github/actions/workflow/status/socialdept/atp-resolver/tests.yml?branch=main&label=tests&style=flat-square"></a>
    <a href="LICENSE" title="Software License"><img src="https://img.shields.io/github/license/socialdept/atp-resolver?style=flat-square"></a>
</p>

---

## What is Resolver?

**Resolver** is a Laravel package that resolves AT Protocol identities. Convert DIDs to handles, find PDS endpoints, and resolve DID documents with automatic caching and fallback support for both `did:plc` and `did:web` methods.

Think of it as a Swiss Army knife for AT Protocol identity resolution.

## Why use Resolver?

- **Simple API** - Resolve DIDs and handles with one method call
- **Automatic caching** - Smart caching with configurable TTLs
- **Multiple DID methods** - Support for `did:plc` and `did:web`
- **PDS discovery** - Find the correct PDS endpoint for any user
- **Production ready** - Battle-tested with proper error handling
- **Zero config** - Works out of the box with sensible defaults

## Quick Example

```php
use SocialDept\AtpResolver\Facades\Resolver;

// Resolve a DID to its document
$document = Resolver::resolveDid('did:plc:ewvi7nxzyoun6zhxrhs64oiz');
$handle = $document->getHandle(); // "user.bsky.social"
$pds = $document->getPdsEndpoint(); // "https://bsky.social"

// Convert a handle to its DID
$did = Resolver::handleToDid('user.bsky.social');
// "did:plc:ewvi7nxzyoun6zhxrhs64oiz"

// Resolve any identity (DID or handle) to a document
$document = Resolver::resolveIdentity('alice.bsky.social');

// Find someone's PDS endpoint
$pds = Resolver::resolvePds('alice.bsky.social');
// "https://bsky.social"
```

## Installation

```bash
composer require socialdept/atp-resolver
```

Resolver will auto-register with Laravel. Optionally publish the config:

```bash
php artisan vendor:publish --tag=resolver-config
```

## Basic Usage

### Resolving DIDs

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

### Resolving Handles

Convert human-readable handles to DIDs or DID documents:

```php
// Convert handle to DID string
$did = Resolver::handleToDid('alice.bsky.social');
// "did:plc:ewvi7nxzyoun6zhxrhs64oiz"

// Resolve handle to full DID document
$document = Resolver::resolveHandle('alice.bsky.social');
$handle = $document->getHandle();
$pds = $document->getPdsEndpoint();
```

### Resolving Identities

Automatically detect and resolve either DIDs or handles:

```php
// Works with DIDs
$document = Resolver::resolveIdentity('did:plc:ewvi7nxzyoun6zhxrhs64oiz');

// Works with handles
$document = Resolver::resolveIdentity('alice.bsky.social');

// Perfect for user input where type is unknown
$actor = $request->input('actor'); // Could be DID or handle
$document = Resolver::resolveIdentity($actor);
```

### Finding PDS Endpoints

Automatically discover a user's Personal Data Server:

```php
// From a DID
$pds = Resolver::resolvePds('did:plc:ewvi7nxzyoun6zhxrhs64oiz');

// From a handle
$pds = Resolver::resolvePds('alice.bsky.social');

// Returns: "https://bsky.social" or user's custom PDS
```

This is particularly useful when you need to make API calls to a user's PDS instead of hardcoding Bluesky's public instance.

### Cache Management

Beacon automatically caches resolutions. Clear the cache when needed:

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

### Disable Caching

Pass `false` as the second parameter to bypass cache:

```php
$document = Resolver::resolveDid('did:plc:abc123', useCache: false);
$did = Resolver::handleToDid('alice.bsky.social', useCache: false);
$document = Resolver::resolveIdentity('alice.bsky.social', useCache: false);
$pds = Resolver::resolvePds('alice.bsky.social', useCache: false);
```

### Identity Validation

Beacon includes static helper methods to validate DIDs and handles:

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

## API Reference

### Available Methods

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

### DidDocument Object

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

## Error Handling

Beacon throws descriptive exceptions when resolution fails:

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

## Use Cases

### Building an AppView

```php
// Resolve user identity from DID
$document = Resolver::resolveDid($event->did);
$handle = $document->getHandle();

// Make authenticated requests to their PDS
$pds = Resolver::resolvePds($event->did);
$client = new AtProtoClient($pds);
```

### Custom Feed Generators

```php
// Resolve multiple handles efficiently (caching kicks in)
$dids = collect(['alice.bsky.social', 'bob.bsky.social'])
    ->map(fn($handle) => Resolver::handleToDid($handle))
    ->all();
```

### Profile Resolution

```php
// Get complete identity information
$document = Resolver::resolveIdentity($username);

$profile = [
    'did' => $document->id,
    'handle' => $document->getHandle(),
    'pds' => $document->getPdsEndpoint(),
];
```

### Input Validation

```php
use SocialDept\AtpResolver\Support\Identity;
use SocialDept\AtpResolver\Facades\Resolver;

// Validate user input before resolving
$actor = request()->input('actor');

if (Identity::isHandle($actor) || Identity::isDid($actor)) {
    $document = Resolver::resolveIdentity($actor);
} else {
    abort(422, 'Invalid handle or DID');
}

// Or convert handle to DID
if (Identity::isHandle($actor)) {
    $did = Resolver::handleToDid($actor);
}
```

## Requirements

- PHP 8.2+
- Laravel 11+
- `ext-gmp` extension

## Resources

- [AT Protocol Documentation](https://atproto.com/)
- [DID:PLC Specification](https://github.com/did-method-plc/did-method-plc)
- [DID:Web Specification](https://w3c-ccg.github.io/did-method-web/)
- [PLC Directory](https://plc.directory/)

## Support & Contributing

Found a bug or have a feature request? [Open an issue](https://github.com/socialdept/atp-resolver/issues).

Want to contribute? We'd love your help! Check out the [contribution guidelines](CONTRIBUTING.md).

## Credits

- [Miguel Batres](https://batres.co) - founder & lead maintainer
- [All contributors](https://github.com/socialdept/atp-resolver/graphs/contributors)

## License

Beacon is open-source software licensed under the [MIT license](LICENSE).

---

**Built for the Federation** • By Social Dept.
