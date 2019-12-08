<?php

declare(strict_types=1);

namespace Version\Tests;

use PHPUnit\Framework\TestCase;
use Version\Tests\TestAsset\VersionIsIdentical;
use Version\Version;

class VersionOperationsTest extends TestCase
{
    /**
     * @test
     */
    public function it_increments_major_version(): void
    {
        $version = Version::fromString('1.10.7');
        $newVersion = $version->incrementMajor();

        $this->assertThat($newVersion, new VersionIsIdentical(2, 0, 0));
    }

    /**
     * @test
     */
    public function it_increments_minor_version(): void
    {
        $version = Version::fromString('2.0.0');
        $newVersion = $version->incrementMinor();

        $this->assertThat($newVersion, new VersionIsIdentical(2, 1, 0));
    }

    /**
     * @test
     */
    public function it_increments_patch_version(): void
    {
        $version = Version::fromString('2.4.3');
        $newVersion = $version->incrementPatch();

        $this->assertThat($newVersion, new VersionIsIdentical(2, 4, 4));
    }

    /**
     * @test
     */
    public function it_sets_pre_release_version(): void
    {
        $version = Version::fromString('2.0.0');
        $newVersion = $version->withPreRelease('beta');

        $this->assertThat($newVersion, new VersionIsIdentical(2, 0, 0, 'beta'));
    }

    /**
     * @test
     */
    public function it_sets_build_metadata(): void
    {
        $version = Version::fromString('2.0.0-beta');
        $newVersion = $version->withBuild('111');

        $this->assertThat($newVersion, new VersionIsIdentical(2, 0, 0, 'beta', '111'));
    }

    /**
     * @test
     */
    public function it_resets_extension_part_when_version_is_incremented(): void
    {
        $version = Version::fromString('2.0.0-beta+111');
        $newVersion = $version->incrementMinor();

        $this->assertThat($newVersion, new VersionIsIdentical(2, 1, 0));
    }

    /**
     * @test
     */
    public function it_resets_build_metadata_when_setting_pre_release_version(): void
    {
        $version = Version::fromString('2.0.0+111');
        $newVersion = $version->withPreRelease('beta');

        $this->assertThat($newVersion, new VersionIsIdentical(2, 0, 0, 'beta'));
    }
}
