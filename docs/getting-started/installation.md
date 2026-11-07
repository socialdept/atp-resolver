# Installation

Part of the [atp-resolver docs](../introduction.md#documentation). Installing the package and its config.

> [!IMPORTANT]
> atp-resolver is deprecated. New projects should install [atp-support](https://socialde.pt/docs/atp-support) instead.

## Installing

```bash
composer require socialdept/atp-resolver
```

The package registers itself with Laravel. Optionally publish the config:

```bash
php artisan vendor:publish --tag=resolver-config
```

## Configuration

The defaults work without any config. To change them, edit `config/resolver.php`:

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
