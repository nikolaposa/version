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

    public static function getNormalVersionsSet()
    {
        return array(
            array('0.9.7', 0, 9, 7),
            array('1.10.0', 1, 10, 0),
            array('2.5.4', 2, 5, 4),
            array('2.1.17', 2, 1, 17),
        );
    }
}
