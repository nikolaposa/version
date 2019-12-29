<?php

declare(strict_types=1);

namespace Version\Tests;

use PHPUnit\Framework\TestCase;
use Version\Exception\CollectionIsEmpty;
use Version\Tests\TestAsset\VersionIsIdentical;
use Version\Tests\TestAsset\VersionCollectionIsIdentical;
use Version\VersionCollection;
use Version\Version;
use Version\Comparison\Constraint\OperationConstraint;

class VersionCollectionTest extends TestCase
{
    /**
     * @test
     */
    public function it_is_created_from_versions_variadic_arguments(): void
    {
        $versions = new VersionCollection(
            Version::fromString('1.0.0'),
            Version::fromString('1.1.0'),
            Version::fromString('2.3.3')
        );

        $this->assertThat($versions, new VersionCollectionIsIdentical([
            [1, 0, 0, null, null],
            [1, 1, 0, null, null],
            [2, 3, 3, null, null],
        ]));
    }

    /**
     * @test
     */
    public function it_is_countable(): void
    {
        $versions = new VersionCollection(
            Version::fromString('1.0.0'),
            Version::fromString('1.1.0'),
            Version::fromString('2.3.3')
        );

        $this->assertCount(3, $versions);
    }

    /**
     * @test
     */
    public function it_checks_for_emptiness(): void
    {
        $versions = new VersionCollection();

        $this->assertTrue($versions->isEmpty());
    }

    /**
     * @test
     */
    public function it_gets_first_version(): void
    {
        $versions = new VersionCollection(
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
    public function it_raises_exception_when_getting_first_item_of_empty_collection(): void
    {
        $versions = new VersionCollection();

        try {
            $versions->first();

            $this->fail('Exception should have been raised');
        } catch (CollectionIsEmpty $ex) {
            $this->assertSame('Cannot get the first Version from an empty collection', $ex->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_gets_last_version(): void
    {
        $versions = new VersionCollection(
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
    public function it_raises_exception_when_getting_last_item_of_empty_collection(): void
    {
        $versions = new VersionCollection();

        try {
            $versions->last();

            $this->fail('Exception should have been raised');
        } catch (CollectionIsEmpty $ex) {
            $this->assertSame('Cannot get the last Version from an empty collection', $ex->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_is_iterable(): void
    {
        $versions = new VersionCollection(
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
    public function it_is_sorted_in_ascending_order(): void
    {
        $versions = new VersionCollection(
            Version::fromString('2.3.3'),
            Version::fromString('1.0.0'),
            Version::fromString('1.1.0'),
            Version::fromString('2.3.3-beta')
        );

        $versions = $versions->sortedAscending();

        $expectedOrder = [
            '1.0.0',
            '1.1.0',
            '2.3.3-beta',
            '2.3.3',
        ];

        /** @var Version[] $versions */
        foreach ($versions as $key => $version) {
            $this->assertSame($expectedOrder[$key], $version->toString());
        }
    }

    /**
     * @test
     */
    public function it_is_sorted_in_descending_order(): void
    {
        $versions = new VersionCollection(
            Version::fromString('2.3.3'),
            Version::fromString('1.0.0'),
            Version::fromString('1.1.0')
        );

        $versions = $versions->sortedDescending();

        $expectedOrder = [
            '2.3.3',
            '1.1.0',
            '1.0.0',
        ];

        /** @var Version[] $versions */
        foreach ($versions as $key => $version) {
            $this->assertSame($expectedOrder[$key], $version->toString());
        }
    }

    /**
     * @test
     */
    public function it_filters_versions_that_match_constraint(): void
    {
        $versions = new VersionCollection(
            Version::fromString('1.0.0'),
            Version::fromString('1.0.1'),
            Version::fromString('2.0.0'),
            Version::fromString('2.0.1')
        );

        $versions2 = $versions->matching(OperationConstraint::fromString('>=2.0.0'));

        $this->assertThat($versions2, new VersionCollectionIsIdentical([
            [2, 0, 0, null, null],
            [2, 0, 1, null, null],
        ]));
    }

    /**
     * @test
     */
    public function it_filters_major_releases(): void
    {
        $releases = new VersionCollection(
            Version::fromString('1.0.0'),
            Version::fromString('1.1.0'),
            Version::fromString('2.0.0'),
            Version::fromString('2.1.0'),
            Version::fromString('3.0.0'),
            Version::fromString('3.0.1')
        );

        $majorReleases = $releases->majorReleases();

        $this->assertThat($majorReleases, new VersionCollectionIsIdentical([
            [1, 0, 0, null, null],
            [2, 0, 0, null, null],
            [3, 0, 0, null, null],
        ]));
    }

    /**
     * @test
     */
    public function it_filters_minor_releases(): void
    {
        $releases = new VersionCollection(
            Version::fromString('1.0.0'),
            Version::fromString('1.1.0'),
            Version::fromString('2.0.0'),
            Version::fromString('2.1.0'),
            Version::fromString('2.1.1')
        );

        $minorReleases = $releases->minorReleases();

        $this->assertThat($minorReleases, new VersionCollectionIsIdentical([
            [1, 1, 0, null, null],
            [2, 1, 0, null, null],
        ]));
    }

    /**
     * @test
     */
    public function it_filters_patch_releases(): void
    {
        $releases = new VersionCollection(
            Version::fromString('1.0.0'),
            Version::fromString('1.0.1'),
            Version::fromString('2.0.0'),
            Version::fromString('2.0.1')
        );

        $patchReleases = $releases->patchReleases();

        $this->assertThat($patchReleases, new VersionCollectionIsIdentical([
            [1, 0, 1, null, null],
            [2, 0, 1, null, null],
        ]));
    }

    /**
     * @test
     */
    public function it_filters_latest_major_release(): void
    {
        $releases = new VersionCollection(
            Version::fromString('1.0.0'),
            Version::fromString('1.1.0'),
            Version::fromString('2.0.0'),
            Version::fromString('2.1.0'),
            Version::fromString('3.0.0'),
            Version::fromString('3.0.1')
        );

        $latestMajorRelease = $releases
            ->majorReleases()
            ->sortedDescending()
            ->first();

        $this->assertThat($latestMajorRelease, new VersionIsIdentical(3, 0, 0));
    }

    /**
     * @test
     */
    public function it_casts_to_array(): void
    {
        $versions = new VersionCollection(
            Version::fromString('1.0.0'),
            Version::fromString('1.0.1'),
            Version::fromString('1.1.0')
        );

        $versionsArray = $versions->toArray();

        $this->assertContainsOnlyInstancesOf(Version::class, $versionsArray);
        $this->assertCount(3, $versionsArray);
    }
}
