<?php

declare(strict_types=1);

namespace Version\Tests;

use PHPUnit\Framework\TestCase;
use Version\Version;
use Version\Extension\PreRelease;
use Version\Extension\Build;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class VersionExtensionTest extends TestCase
{
    public function testVersionPreRelease()
    {
        $version = Version::fromString('1.0.0-alpha');

        $this->assertInstanceOf(PreRelease::class, $version->getPreRelease());

        $identifiers = $version->getPreRelease()->getIdentifiers();
        $this->assertInternalType('array', $identifiers);
        $this->assertCount(1, $identifiers);

        $identifier = current($identifiers);
        $this->assertSame('alpha', $identifier);
    }

    public function testVersionMultiPreRelease()
    {
        $version = Version::fromString('1.0.0-alpha.1.2');

        $identifiers = $version->getPreRelease()->getIdentifiers();
        $this->assertCount(3, $identifiers);

        $id1 = array_shift($identifiers);
        $id2 = array_shift($identifiers);
        $id3 = array_shift($identifiers);

        $this->assertSame('alpha', $id1);
        $this->assertSame('1', $id2);
        $this->assertSame('2', $id3);
    }

    public function testVersionBuild()
    {
        $version = Version::fromString('1.0.0+20150919');

        $this->assertInstanceOf(Build::class, $version->getBuild());

        $identifiers = $version->getBuild()->getIdentifiers();
        $this->assertInternalType('array', $identifiers);
        $this->assertCount(1, $identifiers);

        $identifier = current($identifiers);
        $this->assertSame('20150919', $identifier);
    }

    public function testVersionMultiBuild()
    {
        $version = Version::fromString('1.0.0+exp.sha.5114f85');

        $identifiers = $version->getBuild()->getIdentifiers();
        $this->assertCount(3, $identifiers);

        $id1 = array_shift($identifiers);
        $id2 = array_shift($identifiers);
        $id3 = array_shift($identifiers);

        $this->assertSame('exp', $id1);
        $this->assertSame('sha', $id2);
        $this->assertSame('5114f85', $id3);
    }

    public function testFullVersion()
    {
        $version = Version::fromString('1.0.0-alpha.1+exp.sha.5114f85');

        $preReleaseIdentifiers = $version->getPreRelease()->getIdentifiers();
        $this->assertCount(2, $preReleaseIdentifiers);

        $preReleaseId1 = array_shift($preReleaseIdentifiers);
        $preReleaseId2 = array_shift($preReleaseIdentifiers);

        $this->assertSame('alpha', $preReleaseId1);
        $this->assertSame('1', $preReleaseId2);

        $buildIdentifiers = $version->getBuild()->getIdentifiers();
        $this->assertCount(3, $buildIdentifiers);

        $buildId1 = array_shift($buildIdentifiers);
        $buildId2 = array_shift($buildIdentifiers);
        $buildId3 = array_shift($buildIdentifiers);

        $this->assertSame('exp', $buildId1);
        $this->assertSame('sha', $buildId2);
        $this->assertSame('5114f85', $buildId3);
    }
}
