<?php

namespace SocialDept\AtpResolver\Data;

class DidDocument
{
    /**
     * Create a new DID Document instance.
     *
     * @param  string  $id  The DID (e.g., "did:plc:abc123")
     * @param  array  $alsoKnownAs  Alternative identifiers (handles)
     * @param  array  $verificationMethod  Verification methods (keys)
     * @param  array  $service  Service endpoints (PDS, etc.)
     * @param  array  $raw  The raw DID document
     */
    public function __construct(
        public readonly string $id,
        public readonly array $alsoKnownAs = [],
        public readonly array $verificationMethod = [],
        public readonly array $service = [],
        public readonly array $raw = [],
    ) {
    }

    /**
     * Create a DID Document from a raw array.
     *
     * @param  array  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            alsoKnownAs: $data['alsoKnownAs'] ?? [],
            verificationMethod: $data['verificationMethod'] ?? [],
            service: $data['service'] ?? [],
            raw: $data,
        );
    }

    /**
     * Get the PDS (Personal Data Server) endpoint.
     */
    public function getPdsEndpoint(): ?string
    {
        foreach ($this->service as $service) {
            if (($service['type'] ?? '') === 'AtprotoPersonalDataServer') {
                return $service['serviceEndpoint'] ?? null;
            }
        }

        return null;
    }

    /**
     * Get the handle from alsoKnownAs.
     */
    public function getHandle(): ?string
    {
        foreach ($this->alsoKnownAs as $alias) {
            if (str_starts_with($alias, 'at://')) {
                return substr($alias, 5);
            }
        }

        return null;
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return $this->raw;
    }
}
