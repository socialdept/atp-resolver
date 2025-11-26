<?php

namespace SocialDept\AtpResolver\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SocialDept\AtpResolver\Contracts\DidResolver;
use SocialDept\AtpResolver\Data\DidDocument;
use SocialDept\AtpResolver\Exceptions\DidResolutionException;
use SocialDept\AtpResolver\Resolvers\DidResolverManager;

class DidResolverManagerTest extends TestCase
{
    public function test_it_supports_plc_method_by_default(): void
    {
        $manager = new DidResolverManager();

        $this->assertTrue($manager->supports('plc'));
    }

    public function test_it_supports_web_method_by_default(): void
    {
        $manager = new DidResolverManager();

        $this->assertTrue($manager->supports('web'));
    }

    public function test_it_does_not_support_unknown_methods(): void
    {
        $manager = new DidResolverManager();

        $this->assertFalse($manager->supports('unknown'));
    }

    public function test_it_can_register_custom_resolver(): void
    {
        $manager = new DidResolverManager();

        $customResolver = $this->createMock(DidResolver::class);
        $customResolver->method('supports')->willReturn(true);

        $manager->register('custom', $customResolver);

        $this->assertTrue($manager->supports('custom'));
    }

    public function test_it_throws_exception_for_unsupported_method(): void
    {
        $this->expectException(DidResolutionException::class);
        $this->expectExceptionMessage('Unsupported DID method: unknown');

        $manager = new DidResolverManager();
        $manager->resolve('did:unknown:abc123');
    }

    public function test_it_throws_exception_for_invalid_did_format(): void
    {
        $this->expectException(DidResolutionException::class);
        $this->expectExceptionMessage('Invalid DID format');

        $manager = new DidResolverManager();
        $manager->resolve('not-a-did');
    }

    public function test_it_delegates_to_registered_resolver(): void
    {
        $manager = new DidResolverManager();

        $mockDocument = DidDocument::fromArray(['id' => 'did:custom:abc123']);

        $customResolver = $this->createMock(DidResolver::class);
        $customResolver->expects($this->once())
            ->method('resolve')
            ->with('did:custom:abc123')
            ->willReturn($mockDocument);

        $manager->register('custom', $customResolver);

        $result = $manager->resolve('did:custom:abc123');

        $this->assertSame($mockDocument, $result);
    }
}
