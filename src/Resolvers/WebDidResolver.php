<?php

namespace SocialDept\AtpResolver\Resolvers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use SocialDept\AtpResolver\Contracts\DidResolver;
use SocialDept\AtpResolver\Data\DidDocument;
use SocialDept\AtpResolver\Exceptions\DidResolutionException;
use SocialDept\AtpResolver\Support\Concerns\HasConfig;
use SocialDept\AtpResolver\Support\Concerns\ParsesDid;

class WebDidResolver implements DidResolver
{
    use HasConfig;
    use ParsesDid;

    protected Client $client;

    /**
     * Create a new Web DID resolver instance.
     */
    public function __construct(?int $timeout = null)
    {
        $this->client = new Client([
            'timeout' => $timeout ?? $this->getConfig('resolver.timeout', 10),
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => 'Beacon/1.0',
            ],
        ]);
    }

    /**
     * Resolve a DID:Web to a DID Document.
     *
     * @param  string  $did  The DID to resolve (e.g., "did:web:example.com")
     */
    public function resolve(string $did): DidDocument
    {
        if (! $this->supports($this->extractMethod($did))) {
            throw DidResolutionException::unsupportedMethod($this->extractMethod($did));
        }

        $url = $this->buildDidUrl($did);

        try {
            $response = $this->client->get($url);
            $data = json_decode($response->getBody()->getContents(), true);

            if (! is_array($data)) {
                throw DidResolutionException::resolutionFailed($did, 'Invalid response format');
            }

            return DidDocument::fromArray($data);
        } catch (GuzzleException $e) {
            throw DidResolutionException::resolutionFailed($did, $e->getMessage());
        }
    }

    /**
     * Check if this resolver supports the given DID method.
     *
     * @param  string  $method  The DID method (e.g., "web")
     */
    public function supports(string $method): bool
    {
        return $method === 'web';
    }

    /**
     * Build the URL to fetch the DID document from.
     *
     * @param  string  $did
     */
    protected function buildDidUrl(string $did): string
    {
        $identifier = $this->extractIdentifier($did);

        // Decode URL-encoded characters
        $domain = str_replace('%3A', ':', $identifier);

        // Split domain and path
        $parts = explode(':', $domain);
        $domainName = array_shift($parts);

        // If there's a path, append it; otherwise use .well-known/did.json
        if (count($parts) > 0) {
            $path = implode('/', $parts).'/did.json';
        } else {
            $path = '.well-known/did.json';
        }

        return "https://{$domainName}/{$path}";
    }
}
