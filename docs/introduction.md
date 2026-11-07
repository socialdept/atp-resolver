# Introduction

> [!IMPORTANT]
> atp-resolver is deprecated and no longer maintained. Use [atp-support](https://github.com/socialdept/atp-support) instead. It ships the same resolver with the same method names, plus caching, lexicon resolution and more. See [Moving to atp-support](#moving-to-atp-support).

atp-resolver is a Laravel package that resolves AT Protocol identities. It turns DIDs into DID documents, handles into DIDs, and any identity into its PDS endpoint, with caching and support for both `did:plc` and `did:web`.

The same resolver now ships in [atp-support](https://socialde.pt/docs/atp-support), which every new project should use. These docs stay for apps that still run atp-resolver.

## Moving to atp-support

Swap the package:

```bash
composer remove socialdept/atp-resolver
composer require socialdept/atp-support
```

The resolver keeps every method name and argument from atp-resolver, so the move is a rename of imports, config and env vars.

| atp-resolver | atp-support |
|---|---|
| `SocialDept\AtpResolver\Facades\Resolver` | `SocialDept\AtpSupport\Facades\Resolver` |
| `SocialDept\AtpResolver\Support\Identity` | `SocialDept\AtpSupport\Identity` |
| `SocialDept\AtpResolver\Data\DidDocument` | `SocialDept\AtpSupport\Data\DidDocument` |
| `SocialDept\AtpResolver\Exceptions\*` | `SocialDept\AtpSupport\Exceptions\*` |
| `config/resolver.php`, tag `resolver-config` | `config/atp-support.php`, tag `atp-support-config` |
| `RESOLVER_PLC_DIRECTORY` | `ATP_PLC_DIRECTORY` |
| `RESOLVER_PDS_ENDPOINT` | `ATP_PDS_ENDPOINT` |
| `RESOLVER_TIMEOUT` (default 10) | `ATP_RESOLVER_TIMEOUT` (default 5) |
| `RESOLVER_CACHE_ENABLED` | `ATP_RESOLVER_CACHE_ENABLED` |
| `RESOLVER_CACHE_DID_TTL` | `ATP_RESOLVER_CACHE_DID_TTL` |
| `RESOLVER_CACHE_HANDLE_TTL` | `ATP_RESOLVER_CACHE_HANDLE_TTL` |
| `RESOLVER_CACHE_PDS_TTL` | `ATP_RESOLVER_CACHE_PDS_TTL` |

> [!NOTE]
> The default request timeout drops from 10 seconds to 5. Set `ATP_RESOLVER_TIMEOUT=10` to keep the old value.

atp-support also adds `handleToDidViaDns()`, `handleToDidViaWellKnown()` and `handleToDidViaXrpc()`, plus AT-URIs, NSIDs, lexicon resolution and key helpers. See [the atp-support docs](https://socialde.pt/docs/atp-support).

## Documentation

- **[Getting started](getting-started/README.md)**: [Installation](getting-started/installation.md), [Usage](getting-started/usage.md)
