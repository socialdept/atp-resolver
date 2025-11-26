<?php

namespace SocialDept\AtpResolver\Resolvers;

use SocialDept\AtpResolver\Contracts\DidResolver;
use SocialDept\AtpResolver\Data\DidDocument;
use SocialDept\AtpResolver\Exceptions\DidResolutionException;
use SocialDept\AtpResolver\Support\Concerns\ParsesDid;

class DidResolverManager implements DidResolver
{
    use ParsesDid;

    /**
     * @var array<string, DidResolver>
     */
    protected array $resolvers = [];

    /**
     * Create a new DID resolver manager instance.
     */
    public function __construct()
    {
        $this->registerDefaultResolvers();
    }

    /**
     * Resolve a DID to a DID Document.
     *
     * @param  string  $did  The DID to resolve
     */
    public function resolve(string $did): DidDocument
    {
        $method = $this->extractMethod($did);

        if (! $this->supports($method)) {
            throw DidResolutionException::unsupportedMethod($method);
        }

        return $this->resolvers[$method]->resolve($did);
    }

    /**
     * Check if this resolver supports the given DID method.
     *
     * @param  string  $method  The DID method
     */
    public function supports(string $method): bool
    {
        return isset($this->resolvers[$method]);
    }

    /**
     * Register a DID resolver for a specific method.
     *
     * @param  string  $method
     * @param  DidResolver  $resolver
     */
    public function register(string $method, DidResolver $resolver): self
    {
        $this->resolvers[$method] = $resolver;

        return $this;
    }

    /**
     * Register the default resolvers.
     */
    protected function registerDefaultResolvers(): void
    {
        $this->register('plc', new PlcDidResolver());
        $this->register('web', new WebDidResolver());
    }
}
