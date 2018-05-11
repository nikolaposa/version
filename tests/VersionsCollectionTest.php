<?php

declare(strict_types=1);

namespace Version\Tests;

use PHPUnit\Framework\TestCase;
use Version\Exception\InvalidVersionStringException;
use Version\Tests\TestAsset\VersionIsIdentical;
use Version\Tests\TestAsset\VersionsCollectionIsIdentical;
use Version\VersionsCollection;
use Version\Version;
use Version\Constraint\ComparisonConstraint;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class VersionsCollectionTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_created_via_constructor() : void
    {
        $versions = new VersionsCollection(
            Version::fromString('1.0.0'),
            Version::fromString('1.1.0'),
            Version::fromString('2.3.3')
        );

        $this->assertThat($versions, new VersionsCollectionIsIdentical([
            [1, 0, 0, null, null],
            [1, 1, 0, null, null],
            [2, 3, 3, null, null],
        ]));
    }

    /**
     * @test
     */
    public function it_can_be_created_from_version_strings() : void
    {
        $versions = new VersionsCollection(
            Version::fromString('1.1.0'),
            Version::fromString('2.3.3')
        );

        $this->assertThat($versions, new VersionsCollectionIsIdentical([
            [1, 1, 0, null, null],
            [2, 3, 3, null, null],
        ]));
    }

    /**
     * @test
     */
    public function it_forwards_invalid_version_string_exception() : void
    {
        try {
            new VersionsCollection(
                Version::fromString('1.1.0'),
                Version::fromString('invalid')
            );

            $this->fail('Exception should have been raised');
        } catch (InvalidVersionStringException $ex) {
            $this->assertSame('invalid', $ex->getVersionString());
        }
    }

    /**
     * @test
     */
    public function it_is_countable() : void
    {
        $versions = new VersionsCollection(
            Version::fromString('1.0.0'),
            Version::fromString('1.1.0'),
            Version::fromString('2.3.3')
        );

        $this->assertCount(3, $versions);
    }

    /**
     * @test
     */
    public function it_provides_is_empty_check() : void
    {
        $versions = new VersionsCollection();

        $this->assertTrue($versions->isEmpty());
    }

    /**
     * @test
     */
    public function it_gets_first_version() : void
    {
        $versions = new VersionsCollection(
            Version::fromString('1.0.0'),
            Version::fromString('1.1.0'),
            Version::fromString('2.3.3')
        );

        $version = $versions->first();

        $this->assertNotNull($version);
        $this->assertThat($version, new VersionIsIdentical(1, 0, 0));
    }

    /**
     * @test
     */
    public function it_returns_null_for_first_version_if_collection_is_empty() : void
    {
        $versions = new VersionsCollection();

        $version = $versions->first();

        $this->assertNull($version);
    }

    /**
     * @test
     */
    public function it_gets_last_version() : void
    {
        $versions = new VersionsCollection(
            Version::fromString('1.0.0'),
            Version::fromString('1.1.0'),
            Version::fromString('2.3.3')
        );

        $version = $versions->last();

        $this->assertNotNull($version);
        $this->assertThat($version, new VersionIsIdentical(2, 3, 3));
    }

    /**
     * @test
     */
    public function it_returns_null_for_last_version_if_collection_is_empty() : void
    {
        $versions = new VersionsCollection();

        $version = $versions->last();

        $this->assertNull($version);
    }

    /**
     * @test
     */
    public function it_is_iterable() : void
    {
        $versions = new VersionsCollection(
            Version::fromString('1.0.0'),
            Version::fromString('1.1.0'),
            Version::fromString('2.3.3')
        );

        foreach ($versions as $version) {
            $this->assertInstanceOf(Version::class, $version);
        }
    }

    /**
     * @test
     */
    public function it_is_sorted_in_ascending_order_by_default() : void
    {
        $versions = new VersionsCollection(
            Version::fromString('2.3.3'),
            Version::fromString('1.0.0'),
            Version::fromString('1.1.0'),
            Version::fromString('2.3.3-beta')
        );

        $versions = $versions->sorted();

        $expectedOrder = [
            '1.0.0',
            '1.1.0',
            '2.3.3-beta',
            '2.3.3',
        ];

        foreach ($versions as $key => $version) {
            $this->assertSame($expectedOrder[$key], (string) $version);
        }
    }

    /**
     * @test
     */
    public function it_can_be_sorted_in_descending_order() : void
    {
        $versions = new VersionsCollection(
            Version::fromString('2.3.3'),
            Version::fromString('1.0.0'),
            Version::fromString('1.1.0')
        );

        $versions = $versions->sorted(VersionsCollection::SORT_DESC);

        $expectedOrder = [
            '2.3.3',
            '1.1.0',
            '1.0.0',
        ];

        foreach ($versions as $key => $version) {
            $this->assertSame($expectedOrder[$key], (string) $version);
        }
    }

    /**
     * @test
     */
    public function it_can_be_sorted_via_deprecated_sort_method() : void
    {
        $versions = new VersionsCollection(
            Version::fromString('2.3.3'),
            Version::fromString('1.0.0'),
            Version::fromString('1.1.0'),
            Version::fromString('2.3.3-beta')
        );

        $versions->sort();

        $expectedOrder = [
            '1.0.0',
            '1.1.0',
            '2.3.3-beta',
            '2.3.3',
        ];

        foreach ($versions as $key => $version) {
            $this->assertSame($expectedOrder[$key], (string) $version);
        }
    }

    /**
     * @test
     */
    public function it_filters_versions_that_match_constraint() : void
    {
        $versions = new VersionsCollection(
            Version::fromString('1.0.0'),
            Version::fromString('1.0.1'),
            Version::fromString('2.0.0'),
            Version::fromString('2.0.1')
        );

        $versions2 = $versions->matching(ComparisonConstraint::fromString('>=2.0.0'));

        $this->assertThat($versions2, new VersionsCollectionIsIdentical([
            [2, 0, 0, null, null],
            [2, 0, 1, null, null],
        ]));
    }

    /**
     * @test
     */
    public function it_can_become_empty_after_filtering_out_all_versions() : void
    {
        $versions = new VersionsCollection(
            Version::fromString('1.0.0'),
            Version::fromString('1.0.1'),
            Version::fromString('1.1.0')
        );

        $versions2 = $versions->matching(ComparisonConstraint::fromString('>=2.0.0'));

        $this->assertCount(0, $versions2);
    }

    /**
     * @test
     */
    public function it_can_be_converted_to_an_array() : void
    {
        $versions = new VersionsCollection(
            Version::fromString('1.0.0'),
            Version::fromString('1.0.1'),
            Version::fromString('1.1.0')
        );

        $versionsArray = $versions->toArray();

        $this->assertContainsOnlyInstancesOf(Version::class, $versionsArray);
        $this->assertCount(3, $versionsArray);
    }
}
