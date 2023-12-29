<?php

declare(strict_types=1);

namespace Version\Tests\TestAsset;

use PHPUnit\Framework\Constraint\Constraint;
use Version\Version;
use Version\VersionCollection;

final class VersionCollectionIsIdentical extends Constraint
{
    /** @var VersionIsIdentical[] */
    private array $isIdenticalConstraints = [];

    public function __construct(array $expectedVersions)
    {
        foreach ($expectedVersions as [$expectedMajor, $expectedMinor, $expectedPatch, $expectedPreRelease, $expectedBuild]) {
            $this->isIdenticalConstraints[] = new VersionIsIdentical(
                $expectedMajor,
                $expectedMinor,
                $expectedPatch,
                $expectedPreRelease,
                $expectedBuild
            );
        }
    }

    protected function matches($versions): bool
    {
        /* @var $versions VersionCollection */

        if ($versions->count() !== count($this->isIdenticalConstraints)) {
            return false;
        }

        foreach ($versions->toArray() as $i => $version) {
            /* @var $version Version */

            if (! isset($this->isIdenticalConstraints[$i])) {
                return false;
            }

            $isIdenticalConstraint = $this->isIdenticalConstraints[$i];

            if (!$isIdenticalConstraint->evaluate($version, '', true)) {
                return false;
            }
        }

        return true;
    }

    public function toString(): string
    {
        return 'content is identical for specified versions';
    }
}
