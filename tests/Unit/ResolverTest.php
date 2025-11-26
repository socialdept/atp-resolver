<?php

namespace SocialDept\AtpResolver\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SocialDept\AtpResolver\Contracts\CacheStore;
use SocialDept\AtpResolver\Contracts\DidResolver;
use SocialDept\AtpResolver\Contracts\HandleResolver;
use SocialDept\AtpResolver\Data\DidDocument;
use SocialDept\AtpResolver\Resolver;

class ResolverTest extends TestCase
{
    public function test_it_can_resolve_pds_from_did(): void
    {
        $didResolver = $this->createMock(DidResolver::class);
        $handleResolver = $this->createMock(HandleResolver::class);
        $cache = $this->createMock(CacheStore::class);

        $didDocument = DidDocument::fromArray([
            'id' => 'did:plc:abc123',
            'service' => [
                [
                    'type' => 'AtprotoPersonalDataServer',
                    'serviceEndpoint' => 'https://pds.example.com',
                ],
            ],
        ]);

        $didResolver->expects($this->once())
            ->method('resolve')
            ->with('did:plc:abc123')
            ->willReturn($didDocument);

        $cache->method('has')->willReturn(false);

        // Expect multiple cache puts (DID document + PDS endpoint)
        $cache->expects($this->exactly(2))
            ->method('put')
            ->willReturnCallback(function ($key, $value, $ttl) {
                $this->assertContains($key, ['did:did:plc:abc123', 'pds:did:plc:abc123']);

                return null;
            });

        $beacon = new Resolver($didResolver, $handleResolver, $cache);
        $pds = $beacon->resolvePds('did:plc:abc123');

        $this->assertSame('https://pds.example.com', $pds);
    }

    public function test_it_can_resolve_pds_from_handle(): void
    {
        $didResolver = $this->createMock(DidResolver::class);
        $handleResolver = $this->createMock(HandleResolver::class);
        $cache = $this->createMock(CacheStore::class);

        $handleResolver->expects($this->once())
            ->method('resolve')
            ->with('user.bsky.social')
            ->willReturn('did:plc:abc123');

        $didDocument = DidDocument::fromArray([
            'id' => 'did:plc:abc123',
            'service' => [
                [
                    'type' => 'AtprotoPersonalDataServer',
                    'serviceEndpoint' => 'https://pds.example.com',
                ],
            ],
        ]);

        $didResolver->expects($this->once())
            ->method('resolve')
            ->with('did:plc:abc123')
            ->willReturn($didDocument);

        $cache->method('has')->willReturn(false);

        // Expect multiple cache puts (handle + DID document + PDS endpoint)
        $cache->expects($this->exactly(3))
            ->method('put')
            ->willReturnCallback(function ($key, $value, $ttl) {
                $this->assertContains($key, ['handle:user.bsky.social', 'did:did:plc:abc123', 'pds:user.bsky.social']);

                return null;
            });

        $beacon = new Resolver($didResolver, $handleResolver, $cache);
        $pds = $beacon->resolvePds('user.bsky.social');

        $this->assertSame('https://pds.example.com', $pds);
    }

    public function test_it_returns_null_when_no_pds_endpoint(): void
    {
        $didResolver = $this->createMock(DidResolver::class);
        $handleResolver = $this->createMock(HandleResolver::class);
        $cache = $this->createMock(CacheStore::class);

        $didDocument = DidDocument::fromArray([
            'id' => 'did:plc:abc123',
            'service' => [],
        ]);

        $didResolver->expects($this->once())
            ->method('resolve')
            ->with('did:plc:abc123')
            ->willReturn($didDocument);

        $cache->method('has')->willReturn(false);

        // DID document is still cached, but PDS endpoint is not (since it's null)
        $cache->expects($this->once())
            ->method('put')
            ->with('did:did:plc:abc123', $didDocument, $this->anything());

        $beacon = new Resolver($didResolver, $handleResolver, $cache);
        $pds = $beacon->resolvePds('did:plc:abc123');

        $this->assertNull($pds);
    }

    public function test_it_uses_cached_pds_endpoint(): void
    {
        $didResolver = $this->createMock(DidResolver::class);
        $handleResolver = $this->createMock(HandleResolver::class);
        $cache = $this->createMock(CacheStore::class);

        $cache->expects($this->once())
            ->method('has')
            ->with('pds:did:plc:abc123')
            ->willReturn(true);

        $cache->expects($this->once())
            ->method('get')
            ->with('pds:did:plc:abc123')
            ->willReturn('https://cached-pds.example.com');

        $didResolver->expects($this->never())->method('resolve');

        $beacon = new Resolver($didResolver, $handleResolver, $cache);
        $pds = $beacon->resolvePds('did:plc:abc123');

        $this->assertSame('https://cached-pds.example.com', $pds);
    }

    public function test_it_can_clear_pds_cache(): void
    {
        $didResolver = $this->createMock(DidResolver::class);
        $handleResolver = $this->createMock(HandleResolver::class);
        $cache = $this->createMock(CacheStore::class);

        $cache->expects($this->once())
            ->method('forget')
            ->with('pds:did:plc:abc123');

        $beacon = new Resolver($didResolver, $handleResolver, $cache);
        $beacon->clearPdsCache('did:plc:abc123');
    }
}
