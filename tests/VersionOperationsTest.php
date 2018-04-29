<?php

declare(strict_types=1);

namespace Version\Tests;

use PHPUnit\Framework\TestCase;
use Version\Version;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class VersionOperationsTest extends TestCase
{
    public function testMajorVersionIncrement()
    {
        $version = Version::fromString('1.10.7');
        $newVersion = $version->incrementMajor();

        VersionTest::assertMatchesVersion($newVersion, 2, 0, 0, false, false);
    }

    public function testMinorVersionIncrement()
    {
        $version = Version::fromString('2.0.0');
        $newVersion = $version->incrementMinor();

        VersionTest::assertMatchesVersion($newVersion, 2, 1, 0, false, false);
    }

    public function testPatchVersionIncrement()
    {
        $version = Version::fromString('2.4.3');
        $newVersion = $version->incrementPatch();

        VersionTest::assertMatchesVersion($newVersion, 2, 4, 4, false, false);
    }

    public function testVersionIncrementResetsMetadata()
    {
        $version = Version::fromString('2.0.0-beta+111');
        $newVersion = $version->incrementMinor();

        VersionTest::assertMatchesVersion($newVersion, 2, 1, 0, false, false);
    }

    public function testSettingPreRelease()
    {
        $version = Version::fromString('2.0.0');
        $newVersion = $version->withPreRelease('beta');

        VersionTest::assertMatchesVersion($newVersion, 2, 0, 0, 'beta', false);
    }

    public function testSettingPreReleaseResetsBuild()
    {
        $version = Version::fromString('2.0.0+111');
        $newVersion = $version->withPreRelease('beta');

        VersionTest::assertMatchesVersion($newVersion, 2, 0, 0, 'beta', false);
    }

    public function testSettingBuild()
    {
        $version = Version::fromString('2.0.0-beta');
        $newVersion = $version->withBuild('111');

        VersionTest::assertMatchesVersion($newVersion, 2, 0, 0, 'beta', '111');
    }
}
