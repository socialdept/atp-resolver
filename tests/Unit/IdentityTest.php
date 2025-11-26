<?php

namespace SocialDept\AtpResolver\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SocialDept\AtpResolver\Support\Identity;

class IdentityTest extends TestCase
{
    public function test_it_validates_handles(): void
    {
        $this->assertTrue(Identity::isHandle('alice.bsky.social'));
        $this->assertTrue(Identity::isHandle('user.test'));
        $this->assertTrue(Identity::isHandle('my-handle.example.com'));
        $this->assertTrue(Identity::isHandle('a.b.c.d.example.com'));

        $this->assertFalse(Identity::isHandle(''));
        $this->assertFalse(Identity::isHandle(null));
        $this->assertFalse(Identity::isHandle('invalid'));
        $this->assertFalse(Identity::isHandle('no-tld'));
        $this->assertFalse(Identity::isHandle('.invalid'));
        $this->assertFalse(Identity::isHandle('invalid.'));
    }

    public function test_it_validates_dids(): void
    {
        $this->assertTrue(Identity::isDid('did:plc:ewvi7nxzyoun6zhxrhs64oiz'));
        $this->assertTrue(Identity::isDid('did:web:example.com'));
        $this->assertTrue(Identity::isDid('did:plc:abc123'));
        $this->assertTrue(Identity::isDid('did:web:alice.test'));

        $this->assertFalse(Identity::isDid(''));
        $this->assertFalse(Identity::isDid(null));
        $this->assertFalse(Identity::isDid('invalid'));
        $this->assertFalse(Identity::isDid('did:'));
        $this->assertFalse(Identity::isDid('did:plc:'));
        $this->assertFalse(Identity::isDid('not-a-did'));
    }

    public function test_it_extracts_did_method(): void
    {
        $this->assertSame('plc', Identity::extractDidMethod('did:plc:ewvi7nxzyoun6zhxrhs64oiz'));
        $this->assertSame('web', Identity::extractDidMethod('did:web:example.com'));

        $this->assertNull(Identity::extractDidMethod('invalid'));
        $this->assertNull(Identity::extractDidMethod(''));
    }

    public function test_it_checks_plc_did(): void
    {
        $this->assertTrue(Identity::isPlcDid('did:plc:ewvi7nxzyoun6zhxrhs64oiz'));
        $this->assertFalse(Identity::isPlcDid('did:web:example.com'));
        $this->assertFalse(Identity::isPlcDid('invalid'));
    }

    public function test_it_checks_web_did(): void
    {
        $this->assertTrue(Identity::isWebDid('did:web:example.com'));
        $this->assertFalse(Identity::isWebDid('did:plc:ewvi7nxzyoun6zhxrhs64oiz'));
        $this->assertFalse(Identity::isWebDid('invalid'));
    }
}
