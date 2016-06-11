<?php

/**
 * This file is part of the Version package.
 *
 * Copyright (c) Nikola Posa <posa.nikola@gmail.com>
 *
 * For full copyright and license information, please refer to the LICENSE file,
 * located at the package root folder.
 */

namespace Version\Tests;

use PHPUnit_Framework_TestCase;
use Version\Version;
use Version\Exception\InvalidVersionElementException;
use Version\Exception\InvalidVersionStringException;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class VersionTest extends PHPUnit_Framework_TestCase
{
    public static function assertMatchesVersion(Version $version, $major, $minor, $patch, $preRelease, $build)
    {
        self::assertEquals($version->getMajor(), $major);
        self::assertEquals($version->getMinor(), $minor);
        self::assertEquals($version->getPatch(), $patch);

        if (false === $preRelease) {
            self::assertFalse($version->isPreRelease());
        } else {
            self::assertEquals((string) $version->getPreRelease(), $preRelease);
        }

        if (false === $build) {
            self::assertFalse($version->isBuild());
        } else {
            self::assertEquals((string) $version->getBuild(), $build);
        }
    }

    public function testCreatingFromMajorElement()
    {
        $version = Version::fromMajor(1);

        $this->assertMatchesVersion($version, 1, 0, 0, false, false);
    }

    public function testCreatingFromMinorElement()
    {
        $version = Version::fromMinor(2, 1);

        $this->assertMatchesVersion($version, 2, 1, 0, false, false);
    }

    public function testCreatingFromPatchElement()
    {
        $version = Version::fromPatch(03, 1, 1);

        $this->assertMatchesVersion($version, 3, 1, 1, false, false);
    }

    public function testCreatingFromPreRelease()
    {
        $version = Version::fromPreRelease(2, 0, 0, 'alpha');

        $this->assertMatchesVersion($version, 2, 0, 0, 'alpha', false);
    }

    public function testCreatingFromBuild()
    {
        $version = Version::fromBuild(4, 3, 3, '123');

        $this->assertMatchesVersion($version, 4, 3, 3, false, '123');
    }

    public function testCreatingFromAllElements()
    {
        $version = Version::fromAllElements(1, 0, 0, 'beta', '11');

        $this->assertMatchesVersion($version, 1, 0, 0, 'beta', '11');
    }

    /**
     * @dataProvider versionArrays
     */
    public function testVersionToArrayConversion($versionString, $versionArray)
    {
        $version = Version::fromString($versionString);

        $this->assertEquals($versionArray, $version->toArray());
    }

    public static function versionArrays()
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
     * @dataProvider versionStrings
     */
    public function testCreationFromVersionString($versionString, $major, $minor, $patch, $preRelease, $build)
    {
        $version = Version::fromString($versionString);

        $this->assertMatchesVersion($version, $major, $minor, $patch, $preRelease, $build);
    }

    public static function versionStrings()
    {
        return [
            ['0.9.7', 0, 9, 7, false, false],
            ['1.10.0', 1, 10, 0, false, false],
            ['2.5.4', 2, 5, 4, false, false],
            ['2.1.17', 2, 1, 17, false, false],
            ['3.1.0-beta+123', 3, 1, 0, 'beta', '123'],
        ];
    }

    public function testCreationFailsInCaseOfInvalidMajorVersion()
    {
        $this->setExpectedException(InvalidVersionElementException::class);

        Version::fromMajor('test');
    }

    public function testCreationFailsInCaseOfInvalidMinorVersion()
    {
        $this->setExpectedException(InvalidVersionElementException::class);

        Version::fromMinor(0, -5);
    }

    public function testCreationFailsInCaseOfInvalidPatchVersion()
    {
        $this->setExpectedException(InvalidVersionElementException::class);

        Version::fromPatch(2, 1, 'patch');
    }

    public function testCreationFromStringFailsInCaseOfLeadingZeros()
    {
        $this->setExpectedException(InvalidVersionStringException::class);

        Version::fromString('1.05.2');
    }

    public function testCreationFromStringFailsInCaseInvalidCorePart()
    {
        $this->setExpectedException(InvalidVersionStringException::class);

        Version::fromString('1.5.2.4.4');
    }

    /**
     * @dataProvider printedVersionStrings
     */
    public function testVersionToStringConversion($output, Version $version)
    {
        $this->assertEquals($output, (string) $version);
    }

    /**
     * @dataProvider printedVersionStrings
     */
    public function testJsonSerialization($output, Version $version)
    {
        $this->assertEquals('"' . $output . '"', json_encode($version));
    }

    public static function printedVersionStrings()
    {
        return [
            ['2.1.0', Version::fromPatch(2, 1, 0)],
            ['1.0.0+20150919', Version::fromString('1.0.0+20150919')],
            ['1.0.0+exp.sha.5114f85', Version::fromString('1.0.0+exp.sha.5114f85')],
            ['1.0.0-alpha.1+exp.sha.5114f85', Version::fromString('1.0.0-alpha.1+exp.sha.5114f85')],
        ];
    }
}
