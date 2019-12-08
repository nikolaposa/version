<?php

declare(strict_types=1);

namespace Version\Tests;

use PHPUnit\Framework\TestCase;
use Version\Version;

class VersionExtensionTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_include_pre_release_version(): void
    {
        $version = Version::fromString('1.0.0-alpha');

        $this->assertTrue($version->isPreRelease());
        $identifiers = $version->getPreRelease()->getIdentifiers();
        $this->assertCount(1, $identifiers);
        $this->assertSame('alpha', $identifiers[0]);
    }

    /**
     * @test
     */
    public function it_can_include_multiple_pre_release_version_identifiers(): void
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
    public function it_can_include_build_metadata(): void
    {
        $version = Version::fromString('1.0.0+20150919');

        $this->assertTrue($version->hasBuild());
        $identifiers = $version->getBuild()->getIdentifiers();
        $this->assertCount(1, $identifiers);
        $this->assertSame('20150919', $identifiers[0]);
    }

    /**
     * @test
     */
    public function it_can_include_multiple_build_metadata_identifiers(): void
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
    public function it_can_include_both_pre_release_and_build_extensions(): void
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
