<?php

declare(strict_types=1);

namespace Version\Tests;

use PHPUnit\Framework\TestCase;
use Version\Version;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class VersionExtensionTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_contain_pre_release_identifier() : void
    {
        $version = Version::fromString('1.0.0-alpha');

        $identifiers = $version->getPreRelease()->getIdentifiers();
        $this->assertCount(1, $identifiers);
        $this->assertSame('alpha', $identifiers[0]);
    }

    /**
     * @test
     */
    public function it_can_contain_multiple_pre_release_identifiers() : void
    {
        $version = Version::fromString('1.0.0-alpha.1.2');

        $identifiers = $version->getPreRelease()->getIdentifiers();
        $this->assertCount(3, $identifiers);
        $this->assertSame('alpha', $identifiers[0]);
        $this->assertSame('1', $identifiers[1]);
        $this->assertSame('2', $identifiers[2]);
    }

    /**
     * @test
     */
    public function it_can_contain_build_identifier() : void
    {
        $version = Version::fromString('1.0.0+20150919');

        $identifiers = $version->getBuild()->getIdentifiers();
        $this->assertCount(1, $identifiers);
        $this->assertSame('20150919', $identifiers[0]);
    }

    /**
     * @test
     */
    public function it_can_contain_multiple_build_identifiers() : void
    {
        $version = Version::fromString('1.0.0+exp.sha.5114f85');

        $identifiers = $version->getBuild()->getIdentifiers();
        $this->assertCount(3, $identifiers);
        $this->assertSame('exp', $identifiers[0]);
        $this->assertSame('sha', $identifiers[1]);
        $this->assertSame('5114f85', $identifiers[2]);
    }

    /**
     * @test
     */
    public function it_can_contain_both_pre_release_and_build_identifiers() : void
    {
        $version = Version::fromString('1.0.0-alpha.1+exp.sha.5114f85');

        $preReleaseIdentifiers = $version->getPreRelease()->getIdentifiers();
        $this->assertCount(2, $preReleaseIdentifiers);
        $this->assertSame('alpha', $preReleaseIdentifiers[0]);
        $this->assertSame('1', $preReleaseIdentifiers[1]);

        $buildIdentifiers = $version->getBuild()->getIdentifiers();
        $this->assertCount(3, $buildIdentifiers);
        $this->assertSame('exp', $buildIdentifiers[0]);
        $this->assertSame('sha', $buildIdentifiers[1]);
        $this->assertSame('5114f85', $buildIdentifiers[2]);
    }
}
