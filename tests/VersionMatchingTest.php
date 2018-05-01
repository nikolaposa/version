<?php

declare(strict_types=1);

namespace Version\Tests;

use PHPUnit\Framework\TestCase;
use Version\Constraint\ComparisonConstraint;
use Version\Version;

class VersionMatchingTest extends TestCase
{
    /**
     * @test
     */
    public function it_checks_for_constraint_matching() : void
    {
        $this->assertTrue(Version::fromString('1.1.0')->matches(ComparisonConstraint::fromString('>1.0.0')));
    }
}
