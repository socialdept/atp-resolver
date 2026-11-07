[![Resolver Header](./header.png)](https://github.com/socialdept/atp-resolver)

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

> [!IMPORTANT]
> atp-resolver is deprecated and no longer maintained. Use [atp-support](https://github.com/socialdept/atp-support) instead. It ships the same resolver with the same method names, plus caching, lexicon resolution and more. See [Moving to atp-support](docs/introduction.md#moving-to-atp-support).

atp-resolver resolved AT Protocol identities in Laravel: DIDs to documents, handles to DIDs, and any identity to its PDS, with caching and support for `did:plc` and `did:web`. Its resolver now lives in atp-support, which you'll find documented on [https://socialde.pt/docs/atp-support](https://socialde.pt/docs/atp-support).

## Moving to atp-support

Swap the package:

```bash
composer remove socialdept/atp-resolver
composer require socialdept/atp-support
```

Then change the imports. The method names and arguments stay the same:

```php
// Before
use SocialDept\AtpResolver\Facades\Resolver;
use SocialDept\AtpResolver\Support\Identity;

// After
use SocialDept\AtpSupport\Facades\Resolver;
use SocialDept\AtpSupport\Identity;

$document = Resolver::resolveDid('did:plc:ewvi7nxzyoun6zhxrhs64oiz');
$pds = Resolver::resolvePds('alice.bsky.social');
```

The full list of renamed classes, config keys and env vars is in [the introduction](docs/introduction.md#moving-to-atp-support).

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
