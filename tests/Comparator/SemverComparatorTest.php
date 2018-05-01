<?php

declare(strict_types=1);

namespace Version\Tests\Comparator;

use PHPUnit\Framework\TestCase;
use Version\Comparator\ComparatorInterface;
use Version\Comparator\SemverComparator;
use Version\Version;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class SemverComparatorTest extends TestCase
{
    /**
     * @var ComparatorInterface
     */
    protected $comparator;

    protected function setUp()
    {
        $this->comparator = new SemverComparator();
    }

    /**
     * @test
     * @dataProvider getExpectedResultsOfComparisonVersions
     *
     * @param string $version1String
     * @param string $version2String
     * @param int $expectedResult
     */
    public function it_compares_two_versions(string $version1String, string $version2String, int $expectedResult) : void
    {
        $result = $this->comparator->compare(Version::fromString($version1String), Version::fromString($version2String));

        $this->assertSame($expectedResult, $result);
    }

    public static function getExpectedResultsOfComparisonVersions() : array
    {
        return [
            ['2.1.1', '2.1.0', 1],
            ['1.10.1', '2.1.0', -1],
            ['1.0.0', '1.0.0', 0],
            ['1.0.0', '1.0.1', -1],
            ['1.0.0', '1.0.0-alpha', 1],
            ['1.0.0-alpha', '1.0.0-beta', -1],
            ['1.0.0-alpha.1', '1.0.0-alpha', 1],
            ['1.0.0-alpha.1', '1.0.0-alpha', 1],
            ['1.0.0-alpha.beta', '1.0.0-alpha.1', 1],
            ['1.0.0-beta', '1.0.0-alpha.beta', 1],
            ['1.0.0-beta.11', '1.0.0-beta.2', 1],
            ['1.0.0-rc.1', '1.0.0-beta.11', 1],
            ['1.0.0-rc.1.1', '1.0.0-rc.1', 1],
            ['1.0.0', '1.0.0-rc.1', 1],
            ['1.0.0-alpha+20150919', '1.0.0-alpha+exp.sha.5114f85', 0],
        ];
    }
}
