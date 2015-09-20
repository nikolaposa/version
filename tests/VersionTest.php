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

use Version\Version;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class VersionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getNormalVersionsSet
     */
    public function testCreationFromNormalVersionString($versionString, $major, $minor, $patch)
    {
        $version = Version::fromString($versionString);

        $this->assertEquals($version->getMajor(), $major);
        $this->assertEquals($version->getMinor(), $minor);
        $this->assertEquals($version->getPatch(), $patch);
    }

    /**
     * @expectedException \Version\Exception\InvalidVersionStringException
     */
    public function testCreationFromStringFailsInCaseOfLeadingZeros()
    {
        Version::fromString('1.05.2');
    }

    /**
     * @expectedException \Version\Exception\InvalidArgumentException
     */
    public function testCreationFailsInCaseOfInvalidMajorVersion()
    {
        new Version('test', 1, 1);
    }

    /**
     * @expectedException \Version\Exception\InvalidArgumentException
     */
    public function testCreationFailsInCaseOfInvalidMinorVersion()
    {
        new Version(0, -5, 1);
    }

    /**
     * @expectedException \Version\Exception\InvalidArgumentException
     */
    public function testCreationFailsInCaseOfInvalidPatchVersion()
    {
        new Version(2, 1, 'patch');
    }

    public function testVersionPrinting()
    {
        $this->assertEquals('2.1.0', (string) (new Version(2, 1, 0)));
        $this->assertEquals('1.0.0+20150919', (string) Version::fromString('1.0.0+20150919'));
        $this->assertEquals('1.0.0+exp.sha.5114f85', (string) Version::fromString('1.0.0+exp.sha.5114f85'));
        $this->assertEquals('1.0.0-alpha.1+exp.sha.5114f85', (string) Version::fromString('1.0.0-alpha.1+exp.sha.5114f85'));
    }

    public function testMajorVersionIncrement()
    {
        $version = Version::fromString('1.10.7');
        $newVersion = $version->incrementMajor();

        $this->assertEquals($newVersion->getMajor(), 2);
        $this->assertEquals($newVersion->getMinor(), 0);
        $this->assertEquals($newVersion->getPatch(), 0);
    }

    public function testMajorVersionIncrementWithMetadata()
    {
        $version = Version::fromString('1.10.7');
        $newVersion = $version->incrementMajor(['alpha', '1'], '20150919');

        $this->assertEquals($newVersion->getMajor(), 2);
        $this->assertEquals($newVersion->getMinor(), 0);
        $this->assertEquals($newVersion->getPatch(), 0);
        $this->assertEquals($newVersion->getPreRelease()->getIdentifiers(), ['alpha', '1']);
        $this->assertEquals($newVersion->getBuild()->getIdentifiers(), ['20150919']);
    }

    public function testMinorVersionIncrement()
    {
        $version = Version::fromString('2.4.3');
        $newVersion = $version->incrementMinor();

        $this->assertEquals($newVersion->getMajor(), 2);
        $this->assertEquals($newVersion->getMinor(), 5);
        $this->assertEquals($newVersion->getPatch(), 0);
    }

    public function testMinorVersionIncrementWithMetadata()
    {
        $version = Version::fromString('2.4.3');
        $newVersion = $version->incrementMinor('alpha');

        $this->assertEquals($newVersion->getMajor(), 2);
        $this->assertEquals($newVersion->getMinor(), 5);
        $this->assertEquals($newVersion->getPatch(), 0);
        $this->assertEquals($newVersion->getPreRelease()->getIdentifiers(), ['alpha']);
        $this->assertNull($newVersion->getBuild());
    }

    public function testPatchVersionIncrement()
    {
        $version = Version::fromString('2.4.3');
        $newVersion = $version->incrementPatch();

        $this->assertEquals($newVersion->getMajor(), 2);
        $this->assertEquals($newVersion->getMinor(), 4);
        $this->assertEquals($newVersion->getPatch(), 4);
    }

    public function testPatchVersionIncrementWithMetadata()
    {
        $version = Version::fromString('2.4.3');
        $newVersion = $version->incrementPatch(null, ['20150919']);

        $this->assertEquals($newVersion->getMajor(), 2);
        $this->assertEquals($newVersion->getMinor(), 4);
        $this->assertEquals($newVersion->getPatch(), 4);
        $this->assertNull($newVersion->getPreRelease());
        $this->assertEquals($newVersion->getBuild()->getIdentifiers(), ['20150919']);
    }

    public static function getNormalVersionsSet()
    {
        return [
            ['0.9.7', 0, 9, 7],
            ['1.10.0', 1, 10, 0],
            ['2.5.4', 2, 5, 4],
            ['2.1.17', 2, 1, 17],
        ];
    }
}
