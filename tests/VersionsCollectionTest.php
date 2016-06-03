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
use Version\VersionsCollection;
use Version\Version;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class VersionsCollectionTest extends PHPUnit_Framework_TestCase
{
    public function testCreateCollection()
    {
        $versions = new VersionsCollection([
            Version::fromMajor(1),
            '1.1.0',
            '2.3.3',
        ]);

        $this->assertInstanceOf('Version\VersionsCollection', $versions);
    }

    public function testCollectionCount()
    {
        $versions = new VersionsCollection([
            Version::fromMajor(1),
            '1.1.0',
            '2.3.3',
        ]);

        $this->assertCount(3, $versions);
    }

    public function testCollectionIteration()
    {
        $versions = new VersionsCollection([
            Version::fromMajor(1),
            '1.1.0',
            '2.3.3',
        ]);

        foreach ($versions as $version) {
            $this->assertInstanceOf('Version\Version', $version);
        }
    }

    public function testCollectionSortedInAscendingOrderByDefault()
    {
        $ordered = [
            '1.0.0',
            '1.1.0',
            '2.3.3-beta',
            '2.3.3',
        ];

        $versions = new VersionsCollection([
            '2.3.3',
            Version::fromMajor(1),
            '1.1.0',
            '2.3.3-beta',
        ]);

        $versions->sort();

        foreach ($versions as $key => $version) {
            $this->assertEquals($ordered[$key], (string) $version);
        }
    }

    public function testCollectionSortedInDescendingOrder()
    {
        $ordered = [
            '2.3.3',
            '1.1.0',
            '1.0.0',
        ];

        $versions = new VersionsCollection([
            '2.3.3',
            Version::fromMajor(1),
            '1.1.0',
        ]);

        $versions->sort(VersionsCollection::SORT_DESC);

        foreach ($versions as $key => $version) {
            $this->assertEquals($ordered[$key], (string) $version);
        }
    }
}
