<?php

namespace SocialDept\AtpResolver\Resolvers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use SocialDept\AtpResolver\Contracts\HandleResolver;
use SocialDept\AtpResolver\Exceptions\HandleResolutionException;
use SocialDept\AtpResolver\Support\Concerns\HasConfig;

class AtProtoHandleResolver implements HandleResolver
{
    use HasConfig;

    protected Client $client;

    protected string $pdsEndpoint;

    /**
     * Create a new AT Protocol handle resolver instance.
     *
     * @param  string|null  $pdsEndpoint  The PDS endpoint to use for resolution
     */
    public function __construct(?string $pdsEndpoint = null, ?int $timeout = null)
    {
        $this->pdsEndpoint = $pdsEndpoint ?? $this->getConfig('resolver.pds_endpoint', 'https://bsky.social');
        $this->client = new Client([
            'timeout' => $timeout ?? $this->getConfig('resolver.timeout', 10),
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => 'Beacon/1.0',
            ],
        ]);
    }

    /**
     * Resolve a handle to a DID.
     *
     * @param  string  $handle  The handle to resolve (e.g., "user.bsky.social")
     * @return string The resolved DID
     */
    public function resolve(string $handle): string
    {
        $this->validateHandle($handle);

        try {
            $response = $this->client->get("{$this->pdsEndpoint}/xrpc/com.atproto.identity.resolveHandle", [
                'query' => ['handle' => $handle],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (! isset($data['did'])) {
                throw HandleResolutionException::resolutionFailed($handle, 'No DID in response');
            }

            return $data['did'];
        } catch (GuzzleException $e) {
            throw HandleResolutionException::resolutionFailed($handle, $e->getMessage());
        }
    }

    /**
     * Validate a handle format.
     *
     * @param  string  $handle
     */
    protected function validateHandle(string $handle): void
    {
        // Handle must be a valid domain name
        if (! preg_match('/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $handle)) {
            throw HandleResolutionException::invalidFormat($handle);
        }
    }
}
