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
    public function it_can_increment_major_number() : void
    {
        $version = Version::fromString('1.10.7');
        $newVersion = $version->incrementMajor();

        $this->assertThat($newVersion, new VersionIsIdentical(2, 0, 0));
    }

    /**
     * @test
     */
    public function it_can_increment_minor_number() : void
    {
        $version = Version::fromString('2.0.0');
        $newVersion = $version->incrementMinor();

        $this->assertThat($newVersion, new VersionIsIdentical(2, 1, 0));
    }

    /**
     * @test
     */
    public function it_can_increment_patch_number() : void
    {
        $version = Version::fromString('2.4.3');
        $newVersion = $version->incrementPatch();

        $this->assertThat($newVersion, new VersionIsIdentical(2, 4, 4));
    }

    /**
     * @test
     */
    public function it_resets_extension_part_when_version_is_incremented() : void
    {
        $version = Version::fromString('2.0.0-beta+111');
        $newVersion = $version->incrementMinor();

        $this->assertThat($newVersion, new VersionIsIdentical(2, 1, 0));
    }

    /**
     * @test
     */
    public function it_sets_pre_release_information() : void
    {
        $version = Version::fromString('2.0.0');
        $newVersion = $version->withPreRelease('beta');

        $this->assertThat($newVersion, new VersionIsIdentical(2, 0, 0, 'beta'));
    }

    /**
     * @test
     */
    public function it_resets_build_when_pre_release_is_set() : void
    {
        $version = Version::fromString('2.0.0+111');
        $newVersion = $version->withPreRelease('beta');

        $this->assertThat($newVersion, new VersionIsIdentical(2, 0, 0, 'beta'));
    }

    /**
     * @test
     */
    public function it_sets_build_information() : void
    {
        $version = Version::fromString('2.0.0-beta');
        $newVersion = $version->withBuild('111');

        $this->assertThat($newVersion, new VersionIsIdentical(2, 0, 0, 'beta', '111'));
    }
}
