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
use Version\Exception\InvalidArgumentException;
use Version\Constraint\Constraint;

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

        $this->assertInstanceOf(VersionsCollection::class, $versions);
    }

    public function testCreationViaStaticFactory()
    {
        $versions = VersionsCollection::fromArray([
            Version::fromMajor(1),
            '1.1.0',
            '2.3.3',
        ]);

        $this->assertInstanceOf(VersionsCollection::class, $versions);
    }

    public function testExceptionIsRaisedInCaseOfInvalidVersionItem()
    {
        $this->setExpectedException(
            InvalidArgumentException::class,
            'Item in the versions array should be either string or Version instance, boolean given'
        );

        VersionsCollection::fromArray([
            '1.1.0',
            false,
            '2.3.3',
        ]);
    }

    public function testCollectionCount()
    {
        $versions = VersionsCollection::fromArray([
            Version::fromMajor(1),
            '1.1.0',
            '2.3.3',
        ]);

        $this->assertCount(3, $versions);
    }

    public function testCollectionIteration()
    {
        $versions = VersionsCollection::fromArray([
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

        $versions = VersionsCollection::fromArray([
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

        $versions = VersionsCollection::fromArray([
            '2.3.3',
            Version::fromMajor(1),
            '1.1.0',
        ]);

        $versions->sort(VersionsCollection::SORT_DESC);

        foreach ($versions as $key => $version) {
            $this->assertEquals($ordered[$key], (string) $version);
        }
    }

    public function testSortingCollectionUsingBooleanTrueMeansDescending()
    {
        $ordered = [
            '2.3.3',
            '1.1.0',
            '1.0.0',
        ];

        $versions = VersionsCollection::fromArray([
            '2.3.3',
            Version::fromMajor(1),
            '1.1.0',
        ]);

        $versions->sort(true);

        foreach ($versions as $key => $version) {
            $this->assertEquals($ordered[$key], (string) $version);
        }
    }

    public function testSortingCollectionUsingBooleanFalseMeansAscending()
    {
        $ordered = [
            '1.0.0',
            '1.1.0',
            '2.3.3',
        ];

        $versions = VersionsCollection::fromArray([
            '2.3.3',
            Version::fromMajor(1),
            '1.1.0',
        ]);

        $versions->sort(false);

        foreach ($versions as $key => $version) {
            $this->assertEquals($ordered[$key], (string) $version);
        }
    }

    public function testMatchingCollectionItemsByConstraint()
    {
        $versions = VersionsCollection::fromArray([
            '1.0.0',
            '1.0.1',
            '1.1.0',
            '2.0.0',
            '2.0.1',
        ]);

        $versions2 = $versions->matching(Constraint::fromString('>=2.0.0'));

        $this->assertCount(5, $versions);
        $this->assertCount(2, $versions2);
    }
}
