<?php

namespace SocialDept\AtpResolver\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SocialDept\AtpResolver\Exceptions\DidResolutionException;
use SocialDept\AtpResolver\Resolvers\PlcDidResolver;

class PlcDidResolverTest extends TestCase
{
    public function test_it_supports_plc_method(): void
    {
        $resolver = new PlcDidResolver();

        $this->assertTrue($resolver->supports('plc'));
        $this->assertFalse($resolver->supports('web'));
        $this->assertFalse($resolver->supports('unknown'));
    }

    public function test_it_throws_exception_for_invalid_did_format(): void
    {
        $this->expectException(DidResolutionException::class);
        $this->expectExceptionMessage('Invalid DID format');

        $resolver = new PlcDidResolver();
        $resolver->resolve('invalid-did');
    }

    public function test_it_throws_exception_for_incomplete_did(): void
    {
        $this->expectException(DidResolutionException::class);
        $this->expectExceptionMessage('Invalid DID format');

        $resolver = new PlcDidResolver();
        $resolver->resolve('did:plc');
    }

    public function test_it_throws_exception_for_unsupported_method(): void
    {
        $this->expectException(DidResolutionException::class);
        $this->expectExceptionMessage('Unsupported DID method: web');

        $resolver = new PlcDidResolver();
        $resolver->resolve('did:web:example.com');
    }
}
