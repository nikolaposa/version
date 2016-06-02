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
use Version\Tests\VersionTest;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class VersionOperationsTest extends PHPUnit_Framework_TestCase
{
    public function testMajorVersionIncrement()
    {
        $version = Version::fromString('1.10.7');
        $newVersion = $version->incrementMajor();

        VersionTest::assertMatchesVersion($newVersion, 2, 0, 0, false, false);
    }

    public function testMajorVersionIncrementWithMetadata()
    {
        $version = Version::fromString('1.10.7');
        $newVersion = $version->incrementMajor(['alpha', '1'], '20150919');

        VersionTest::assertMatchesVersion($newVersion, 2, 0, 0, 'alpha.1', '20150919');
    }

    public function testMinorVersionIncrement()
    {
        $version = Version::fromString('2.4.3');
        $newVersion = $version->incrementMinor();

        VersionTest::assertMatchesVersion($newVersion, 2, 5, 0, false, false);
    }

    public function testMinorVersionIncrementWithMetadata()
    {
        $version = Version::fromString('2.4.3');
        $newVersion = $version->incrementMinor('alpha');

        VersionTest::assertMatchesVersion($newVersion, 2, 5, 0, 'alpha', false);
    }

    public function testPatchVersionIncrement()
    {
        $version = Version::fromString('2.4.3');
        $newVersion = $version->incrementPatch();

        VersionTest::assertMatchesVersion($newVersion, 2, 4, 4, false, false);
    }

    public function testPatchVersionIncrementWithMetadata()
    {
        $version = Version::fromString('2.4.3');
        $newVersion = $version->incrementPatch(null, ['20150919']);

        VersionTest::assertMatchesVersion($newVersion, 2, 4, 4, false, '20150919');
    }
}
