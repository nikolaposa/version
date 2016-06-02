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
    private function assertMatchesVersion(Version $version, $major, $minor, $patch, $preRelease, $build)
    {
        $this->assertEquals($version->getMajor(), $major);
        $this->assertEquals($version->getMinor(), $minor);
        $this->assertEquals($version->getPatch(), $patch);

        if (false === $preRelease) {
            $this->assertFalse($version->isPreRelease());
        } else {
            $this->assertEquals((string) $version->getPreRelease(), $preRelease);
        }

        if (false === $build) {
            $this->assertFalse($version->isBuild());
        } else {
            $this->assertEquals((string) $version->getBuild(), $build);
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

    /**
     * @dataProvider printedVersionStrings
     */
    public function testVersionToStringConversion($output, Version $version)
    {
        $this->assertEquals($output, (string) $version);
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

    public function testMajorVersionIncrement()
    {
        $version = Version::fromString('1.10.7');
        $newVersion = $version->incrementMajor();

        $this->assertMatchesVersion($newVersion, 2, 0, 0, false, false);
    }

    public function testMajorVersionIncrementWithMetadata()
    {
        $version = Version::fromString('1.10.7');
        $newVersion = $version->incrementMajor(['alpha', '1'], '20150919');

        $this->assertMatchesVersion($newVersion, 2, 0, 0, 'alpha.1', '20150919');
    }

    public function testMinorVersionIncrement()
    {
        $version = Version::fromString('2.4.3');
        $newVersion = $version->incrementMinor();

        $this->assertMatchesVersion($newVersion, 2, 5, 0, false, false);
    }

    public function testMinorVersionIncrementWithMetadata()
    {
        $version = Version::fromString('2.4.3');
        $newVersion = $version->incrementMinor('alpha');

        $this->assertMatchesVersion($newVersion, 2, 5, 0, 'alpha', false);
    }

    public function testPatchVersionIncrement()
    {
        $version = Version::fromString('2.4.3');
        $newVersion = $version->incrementPatch();

        $this->assertMatchesVersion($newVersion, 2, 4, 4, false, false);
    }

    public function testPatchVersionIncrementWithMetadata()
    {
        $version = Version::fromString('2.4.3');
        $newVersion = $version->incrementPatch(null, ['20150919']);

        $this->assertMatchesVersion($newVersion, 2, 4, 4, false, '20150919');
    }

    public function testCreationFailsInCaseOfInvalidMajorVersion()
    {
        $this->expectException(InvalidVersionElementException::class);

        Version::fromMajor('test');
    }

    public function testCreationFailsInCaseOfInvalidMinorVersion()
    {
        $this->expectException(InvalidVersionElementException::class);

        Version::fromMinor(0, -5);
    }

    public function testCreationFailsInCaseOfInvalidPatchVersion()
    {
        $this->expectException(InvalidVersionElementException::class);

        Version::fromPatch(2, 1, 'patch');
    }

    public function testCreationFromStringFailsInCaseOfLeadingZeros()
    {
        $this->expectException(InvalidVersionStringException::class);

        Version::fromString('1.05.2');
    }

    public function testCreationFromStringFailsInCaseInvalidCorePart()
    {
        $this->expectException(InvalidVersionStringException::class);

        Version::fromString('1.5.2.4.4');
    }
}
