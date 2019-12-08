<?php

declare(strict_types=1);

namespace Version\Tests\Comparison;

use PHPUnit\Framework\TestCase;
use Version\Comparison\Comparator;
use Version\Comparison\SemverComparator;
use Version\Version;

class SemverComparatorTest extends TestCase
{
    /** @var Comparator */
    protected $comparator;

    protected function setUp(): void
    {
        $this->comparator = new SemverComparator();
    }

    /**
     * @test
     * @dataProvider getExpectedComparisonResults
     *
     * @param string $version1String
     * @param string $version2String
     * @param int $expectedResult
     */
    public function it_compares_two_versions(string $version1String, string $version2String, int $expectedResult): void
    {
        $result = $this->comparator->compare(Version::fromString($version1String), Version::fromString($version2String));

        $this->assertSame($expectedResult, $result);
    }

    public static function getExpectedComparisonResults(): array
    {
        return [
            'major' => ['1.10.1', '2.1.0', -1],
            'minor' => ['1.0.0', '1.1.0', -1],
            'patch' => ['2.1.1', '2.1.0', 1],
            'same' => ['1.0.0', '1.0.0', 0],
            'regularVsPreRelease' => ['1.0.0', '1.0.0-alpha', 1],
            'preReleaseAlphabeticalComparison' => ['1.0.0-alpha', '1.0.0-beta', -1],
            'preReleaseAlphabeticalIdentifiersComparedInOrder' => ['1.0.0-alpha.beta', '1.0.0-beta', -1],
            'preReleaseNumericalIdentifiersComparedInOrder' => ['1.0.0-3.alpha', '1.0.0-1.beta', 1],
            'longerPreReleaseIsGreaterIfIdentifiersAreTheSame' => ['1.0.0-alpha.1', '1.0.0-alpha', 1],
            'multiIdentifierPreReleaseAlphabeticalComparison' => ['1.0.0-alpha.beta', '1.0.0-alpha.1', 1],
            'numericPreReleaseIdentifiers' => ['1.0.0-beta.11', '1.0.0-beta.2', 1],
            'rcVsBeta' => ['1.0.0-rc.1', '1.0.0-beta.11', 1],
            'buildPartIgnored' => ['1.0.0-alpha+20150919', '1.0.0-alpha+exp.sha.5114f85', 0],
            'alphanumericPreReleases' => ['1.0.0-b1', '1.0.0-a', 1],
        ];
    }
}
