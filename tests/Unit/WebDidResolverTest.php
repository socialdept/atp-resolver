<?php

namespace SocialDept\AtpResolver\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SocialDept\AtpResolver\Exceptions\DidResolutionException;
use SocialDept\AtpResolver\Resolvers\WebDidResolver;

class WebDidResolverTest extends TestCase
{
    public function test_it_supports_web_method(): void
    {
        $resolver = new WebDidResolver();

        $this->assertTrue($resolver->supports('web'));
        $this->assertFalse($resolver->supports('plc'));
        $this->assertFalse($resolver->supports('unknown'));
    }

    public function test_it_throws_exception_for_invalid_did_format(): void
    {
        $this->expectException(DidResolutionException::class);
        $this->expectExceptionMessage('Invalid DID format');

        $resolver = new WebDidResolver();
        $resolver->resolve('invalid-did');
    }

    public function test_it_throws_exception_for_unsupported_method(): void
    {
        $this->expectException(DidResolutionException::class);
        $this->expectExceptionMessage('Unsupported DID method: plc');

        $resolver = new WebDidResolver();
        $resolver->resolve('did:plc:abc123');
    }
}
