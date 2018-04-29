<?php

declare(strict_types=1);

namespace Version\Tests;

use PHPUnit\Framework\TestCase;
use Version\VersionsCollection;
use Version\Version;
use Version\Constraint\Constraint;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class VersionsCollectionTest extends TestCase
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
