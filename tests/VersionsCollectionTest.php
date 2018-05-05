<?php

declare(strict_types=1);

namespace Version\Tests;

use PHPUnit\Framework\TestCase;
use Version\Exception\InvalidVersionStringException;
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
            Version::fromParts(1),
            Version::fromString('1.1.0'),
            Version::fromString('2.3.3')
        );

        $this->assertInstanceOf(VersionsCollection::class, $versions);
    }

    /**
     * @test
     */
    public function it_can_be_created_from_version_strings() : void
    {
        $versions = VersionsCollection::fromStrings('1.1.0', '2.3.3');

        $this->assertInstanceOf(VersionsCollection::class, $versions);
    }

    /**
     * @test
     */
    public function it_forwards_invalid_version_string_exception() : void
    {
        try {
            VersionsCollection::fromStrings('1.1.0', 'invalid');

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
            Version::fromParts(1),
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
    public function it_is_iterable() : void
    {
        $versions = new VersionsCollection(
            Version::fromParts(1),
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
        $ordered = [
            '1.0.0',
            '1.1.0',
            '2.3.3-beta',
            '2.3.3',
        ];

        $versions = new VersionsCollection(
            Version::fromString('2.3.3'),
            Version::fromParts(1),
            Version::fromString('1.1.0'),
            Version::fromString('2.3.3-beta')
        );

        $versions->sort();

        foreach ($versions as $key => $version) {
            $this->assertSame($ordered[$key], (string) $version);
        }
    }

    /**
     * @test
     */
    public function it_can_be_sorted_in_descending_order() : void
    {
        $ordered = [
            '2.3.3',
            '1.1.0',
            '1.0.0',
        ];

        $versions = new VersionsCollection(
            Version::fromString('2.3.3'),
            Version::fromParts(1),
            Version::fromString('1.1.0')
        );

        $versions->sort(VersionsCollection::SORT_DESC);

        foreach ($versions as $key => $version) {
            $this->assertSame($ordered[$key], (string) $version);
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
            Version::fromString('1.1.0'),
            Version::fromString('2.0.0'),
            Version::fromString('2.0.1')
        );

        $versions2 = $versions->matching(ComparisonConstraint::fromString('>=2.0.0'));

        $this->assertCount(5, $versions);
        $this->assertCount(2, $versions2);
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
        $versions = VersionsCollection::fromStrings(
            '1.0.0',
            '1.0.1',
            '1.1.0'
        );

        $versionsArray = $versions->toArray();

        $this->assertContainsOnlyInstancesOf(Version::class, $versionsArray);
        $this->assertCount(3, $versionsArray);
    }
}
