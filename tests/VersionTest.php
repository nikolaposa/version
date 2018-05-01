<?php

declare(strict_types=1);

namespace Version\Tests;

use PHPUnit\Framework\TestCase;
use Version\Exception\InvalidVersionPartException;
use Version\Exception\InvalidVersionStringException;
use Version\Extension\Build;
use Version\Extension\NoBuild;
use Version\Extension\NoPreRelease;
use Version\Extension\PreRelease;
use Version\Version;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class VersionTest extends TestCase
{
    public static function assertMatchesVersion(Version $version, int $major, int $minor, int $patch, $preRelease, $build) : void
    {
        self::assertSame($version->getMajor(), $major);
        self::assertSame($version->getMinor(), $minor);
        self::assertSame($version->getPatch(), $patch);

        if (false === $preRelease) {
            self::assertFalse($version->isPreRelease());
        } else {
            self::assertSame((string) $version->getPreRelease(), $preRelease);
        }

        if (false === $build) {
            self::assertFalse($version->isBuild());
        } else {
            self::assertSame((string) $version->getBuild(), $build);
        }
    }

    /**
     * @test
     */
    public function it_is_created_from_parts() : void
    {
        $version = Version::fromParts(1, 0, 0, PreRelease::fromIdentifiersString('beta'), Build::fromIdentifiersString('11'));

        $this->assertMatchesVersion($version, 1, 0, 0, 'beta', '11');
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
    public function it_can_be_created_from_string(string $versionString, int $major, int $minor, int $patch, $preRelease, $build) : void
    {
        $version = Version::fromString($versionString);

        $this->assertMatchesVersion($version, $major, $minor, $patch, $preRelease, $build);
    }

    public static function getVersionStrings() : array
    {
        return [
            ['0.9.7', 0, 9, 7, false, false],
            ['1.10.0', 1, 10, 0, false, false],
            ['2.5.4', 2, 5, 4, false, false],
            ['2.1.17', 2, 1, 17, false, false],
            ['3.1.0-beta+123', 3, 1, 0, 'beta', '123'],
            ['v1.2.3', 1, 2, 3, false, false],
        ];
    }

    /**
     * @test
     * @dataProvider getPrintedVersionStrings
     *
     * @param string $versionString
     * @param Version $version
     */
    public function it_can_be_converted_to_string(Version $version, string $versionString) : void
    {
        $this->assertSame($versionString, (string) $version);
    }

    /**
     * @test
     * @dataProvider getPrintedVersionStrings
     *
     * @param string $versionString
     * @param Version $version
     */
    public function it_can_be_serialized_to_json(Version $version, string $versionString) : void
    {
        $this->assertSame('"' . $versionString . '"', json_encode($version));
    }

    public static function getPrintedVersionStrings() : array
    {
        return [
            [Version::fromParts(2, 1), '2.1.0'],
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
    public function it_can_be_converted_to_an_array(string $versionString, array $versionArray) : void
    {
        $version = Version::fromString($versionString);

        $this->assertSame($versionArray, $version->toArray());
    }

    public static function getVersionArrays() : array
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
    public function it_raises_exception_when_created_with_invalid_major_version() : void
    {
        try {
            Version::fromParts(-10, 0, 0, new NoPreRelease(), new NoBuild());

            $this->fail('Exception should have been raised');
        } catch (InvalidVersionPartException $ex) {
            $this->assertSame('Major version must be non-negative integer', $ex->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_raises_exception_when_created_with_invalid_minor_version() : void
    {
        try {
            Version::fromParts(0, -5, 1, new NoPreRelease(), new NoBuild());

            $this->fail('Exception should have been raised');
        } catch (InvalidVersionPartException $ex) {
            $this->assertSame('Minor version must be non-negative integer', $ex->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_raises_exception_when_created_with_invalid_patch_version() : void
    {
        try {
            Version::fromParts(2, 1, -1, new NoPreRelease(), new NoBuild());

            $this->fail('Exception should have been raised');
        } catch (InvalidVersionPartException $ex) {
            $this->assertSame('Patch version must be non-negative integer', $ex->getMessage());
        }
    }

    /**
     * @test
     * @dataProvider getInvalidVersionStrings
     *
     * @param string $invalidVersion
     */
    public function it_raises_exception_when_created_with_invalid_version_string(string $invalidVersion) : void
    {
        try {
            Version::fromString($invalidVersion);

            $this->fail('Exception should have been raised');
        } catch (InvalidVersionStringException $ex) {
            $this->assertSame("Version string '$invalidVersion' is not valid and cannot be parsed", $ex->getMessage());
            $this->assertSame($invalidVersion, $ex->getVersionString());
        }
    }

    public function getInvalidVersionStrings() : array
    {
        return [
            'tooManySubVersions' => ['1.5.2.4.4'],
            'leadingZeroIsInvalid' => ['1.05.2'],
        ];
    }
}
