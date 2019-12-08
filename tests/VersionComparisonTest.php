<?php

declare(strict_types=1);

namespace Version\Tests;

use PHPUnit\Framework\TestCase;
use Version\Comparison\Constraint\OperationConstraint;
use Version\Version;

class VersionComparisonTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_compared_to_other_version(): void
    {
        $this->assertSame(1, Version::fromString('2.1.1')->compareTo(Version::fromString('2.1.0')));
    }

    /**
     * @test
     */
    public function it_can_be_compared_using_strings(): void
    {
        $this->assertSame(0, Version::fromString('2.0.0')->compareTo('2.0.0'));
    }

    /**
     * @test
     */
    public function it_supports_is_equal_comparison(): void
    {
        $this->assertTrue(Version::fromString('1.0.0')->isEqualTo('1.0.0'));
    }

    /**
     * @test
     */
    public function it_supports_not_equal_comparison(): void
    {
        $this->assertTrue(Version::fromString('1.0.0')->isNotEqualTo('2.0.0'));
    }

    /**
     * @test
     */
    public function it_supports_greater_than_comparison(): void
    {
        $this->assertTrue(Version::fromString('1.0.1')->isGreaterThan('1.0.0'));
    }

    /**
     * @test
     */
    public function it_supports_greater_or_equal_comparison(): void
    {
        $this->assertTrue(Version::fromString('1.0.0')->isGreaterOrEqualTo('1.0.0'));
    }

    /**
     * @test
     */
    public function it_supports_less_than_comparison(): void
    {
        $this->assertTrue(Version::fromString('1.0.1')->isLessThan('1.0.2'));
    }

    /**
     * @test
     */
    public function it_supports_less_or_equal_comparison(): void
    {
        $this->assertTrue(Version::fromString('1.0.0')->isLessOrEqualTo('1.0.0'));
    }

    /**
     * @test
     */
    public function it_supports_matching_comparision_constraint(): void
    {
        $this->assertTrue(Version::fromString('1.1.0')->matches(OperationConstraint::fromString('>1.0.0')));
    }
}
