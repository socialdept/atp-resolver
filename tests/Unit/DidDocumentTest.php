<?php

namespace SocialDept\AtpResolver\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SocialDept\AtpResolver\Data\DidDocument;

class DidDocumentTest extends TestCase
{
    public function test_it_can_be_created_from_array(): void
    {
        $data = [
            'id' => 'did:plc:abc123',
            'alsoKnownAs' => ['at://user.bsky.social'],
            'verificationMethod' => [],
            'service' => [
                [
                    'type' => 'AtprotoPersonalDataServer',
                    'serviceEndpoint' => 'https://bsky.social',
                ],
            ],
        ];

        $document = DidDocument::fromArray($data);

        $this->assertSame('did:plc:abc123', $document->id);
        $this->assertSame(['at://user.bsky.social'], $document->alsoKnownAs);
        $this->assertCount(1, $document->service);
    }

    public function test_it_can_get_pds_endpoint(): void
    {
        $document = DidDocument::fromArray([
            'id' => 'did:plc:abc123',
            'service' => [
                [
                    'type' => 'AtprotoPersonalDataServer',
                    'serviceEndpoint' => 'https://bsky.social',
                ],
            ],
        ]);

        $this->assertSame('https://bsky.social', $document->getPdsEndpoint());
    }

    public function test_it_returns_null_when_no_pds_endpoint(): void
    {
        $document = DidDocument::fromArray([
            'id' => 'did:plc:abc123',
            'service' => [],
        ]);

        $this->assertNull($document->getPdsEndpoint());
    }

    public function test_it_can_get_handle(): void
    {
        $document = DidDocument::fromArray([
            'id' => 'did:plc:abc123',
            'alsoKnownAs' => ['at://user.bsky.social'],
        ]);

        $this->assertSame('user.bsky.social', $document->getHandle());
    }

    public function test_it_returns_null_when_no_handle(): void
    {
        $document = DidDocument::fromArray([
            'id' => 'did:plc:abc123',
            'alsoKnownAs' => [],
        ]);

        $this->assertNull($document->getHandle());
    }

    public function test_it_can_convert_to_array(): void
    {
        $data = [
            'id' => 'did:plc:abc123',
            'alsoKnownAs' => ['at://user.bsky.social'],
        ];

        $document = DidDocument::fromArray($data);

        $this->assertSame($data, $document->toArray());
    }
}
