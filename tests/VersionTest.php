<?php

declare(strict_types=1);

namespace Version\Tests;

use PHPUnit\Framework\TestCase;
use Version\Comparison\Comparator;
use Version\Exception\InvalidVersion;
use Version\Exception\InvalidVersionString;
use Version\Extension\Build;
use Version\Extension\PreRelease;
use Version\Tests\TestAsset\VersionIsIdentical;
use Version\Version;

class VersionTest extends TestCase
{
    /**
     * @test
     */
    public function it_is_created_from_version_parts(): void
    {
        $version = Version::from(
            1,
            0,
            0,
            PreRelease::fromString('beta'),
            Build::fromString('11')
        );

        $this->assertThat($version, new VersionIsIdentical(1, 0, 0, 'beta', '11'));
    }

    /**
     * @test
     * @dataProvider getVersionStrings
     *
     * @param string $versionString
     * @param int $major
     * @param int $minor
     * @param int $patch
     * @param string|bool $preRelease
     * @param string|bool $build
     */
    public function it_can_be_created_from_string(string $versionString, int $major, int $minor, int $patch, $preRelease, $build): void
    {
        $version = Version::fromString($versionString);

        $this->assertThat($version, new VersionIsIdentical($major, $minor, $patch, $preRelease, $build));
    }

    public static function getVersionStrings(): array
    {
        return [
            ['0.9.7', 0, 9, 7, null, null],
            ['1.10.0', 1, 10, 0, null, null],
            ['2.5.4', 2, 5, 4, null, null],
            ['2.1.17', 2, 1, 17, null, null],
            ['3.1.0-beta+123', 3, 1, 0, 'beta', '123'],
            ['v1.2.3', 1, 2, 3, null, null],
            ['release-1.2.3', 1, 2, 3, null, null],
        ];
    }

    /**
     * @test
     * @dataProvider getPrintedVersionStrings
     *
     * @param string $versionString
     * @param Version $version
     */
    public function it_casts_to_string(Version $version, string $versionString): void
    {
        $this->assertSame($versionString, $version->toString());
    }

    /**
     * @test
     * @dataProvider getPrintedVersionStrings
     *
     * @param string $versionString
     * @param Version $version
     */
    public function it_casts_to_json(Version $version, string $versionString): void
    {
        $this->assertSame('"' . $versionString . '"', json_encode($version));
    }

    public static function getPrintedVersionStrings(): array
    {
        return [
            [Version::from(2, 1), '2.1.0'],
            [Version::fromString('1.0.0+20150919'), '1.0.0+20150919'],
            [Version::fromString('1.0.0+exp.sha.5114f85'), '1.0.0+exp.sha.5114f85'],
            [Version::fromString('1.0.0-alpha.1+exp.sha.5114f85'), '1.0.0-alpha.1+exp.sha.5114f85'],
        ];
    }

    /**
     * @test
     * @dataProvider getVersionArrays
     *
     * @param string $versionString
     * @param array $versionArray
     */
    public function it_casts_to_array(string $versionString, array $versionArray): void
    {
        $version = Version::fromString($versionString);

        $this->assertSame($versionArray, $version->toArray());
    }

    public static function getVersionArrays(): array
    {
        return [
            [
                '1.7.3',
                ['major' => 1, 'minor' => 7, 'patch' => 3, 'preRelease' => [], 'build' => []]
            ],
            [
                '2.0.0-alpha',
                ['major' => 2, 'minor' => 0, 'patch' => 0, 'preRelease' => ['alpha'], 'build' => []]
            ],
            [
                '1.11.3+111',
                ['major' => 1, 'minor' => 11, 'patch' => 3, 'preRelease' => [], 'build' => ['111']]
            ],
            [
                '3.0.0-beta.1+1.2.3',
                ['major' => 3, 'minor' => 0, 'patch' => 0, 'preRelease' => ['beta', '1'], 'build' => ['1', '2', '3']]
            ],
        ];
    }

    /**
     * @test
     */
    public function it_is_serializable(): void
    {
        $version = Version::from(
            1,
            0,
            0,
            PreRelease::fromString('beta'),
            Build::fromString('123')
        );

        $serializedVersion = serialize($version);

        /** @var Version $deserializedVersion */
        $deserializedVersion = unserialize($serializedVersion);

        $this->assertTrue($deserializedVersion->isEqualTo($version));
    }

    /**
     * @test
     */
    public function it_validates_major_version_number(): void
    {
        try {
            Version::from(-10, 0, 0, PreRelease::empty(), Build::empty());

            $this->fail('Exception should have been raised');
        } catch (InvalidVersion $ex) {
            $this->assertSame('Major version must be positive integer', $ex->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_validates_minor_version_number(): void
    {
        try {
            Version::from(0, -5, 1, PreRelease::empty(), Build::empty());

            $this->fail('Exception should have been raised');
        } catch (InvalidVersion $ex) {
            $this->assertSame('Minor version must be positive integer', $ex->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_validates_patch_version_number(): void
    {
        try {
            Version::from(2, 1, -1, PreRelease::empty(), Build::empty());

            $this->fail('Exception should have been raised');
        } catch (InvalidVersion $ex) {
            $this->assertSame('Patch version must be positive integer', $ex->getMessage());
        }
    }

    /**
     * @test
     * @dataProvider getInvalidVersionStrings
     *
     * @param string $invalidVersion
     */
    public function it_validates_version_string_input(string $invalidVersion): void
    {
        try {
            Version::fromString($invalidVersion);

            $this->fail('Exception should have been raised');
        } catch (InvalidVersionString $ex) {
            $this->assertSame("Version string '$invalidVersion' is not valid and cannot be parsed", $ex->getMessage());
            $this->assertSame($invalidVersion, $ex->getVersionString());
        }
    }

    public function getInvalidVersionStrings(): array
    {
        return [
            'tooManySubVersions' => ['1.5.2.4.4'],
            'leadingZeroIsInvalid' => ['1.05.2'],
        ];
    }

    /**
     * @test
     */
    public function it_allows_setting_custom_comparator_strategy(): void
    {
        Version::setComparator(new class implements Comparator {
            public function compare(Version $version1, Version $version2): int
            {
                return 1;
            }
        });

        try {
            $version = Version::from(1);
            $this->assertSame(1, $version->compareTo($version));
        } finally {
            // reset comparator
            Version::setComparator(null);
        }
    }
}
