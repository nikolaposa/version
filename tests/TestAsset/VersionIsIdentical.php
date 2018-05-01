<?php

declare(strict_types=1);

namespace Version\Tests\TestAsset;

use PHPUnit\Framework\Constraint\Constraint;
use Version\Extension\Build;
use Version\Extension\PreRelease;
use Version\Version;

final class VersionIsIdentical extends Constraint
{
    /**
     * @var Version
     */
    private $expectedVersion;

    public function __construct(
        int $expectedMajor,
        int $expectedMinor,
        int $expectedPatch,
        $expectedPreRelease = null,
        $expectedBuild = null
    ) {
        parent::__construct();

        $this->expectedVersion = Version::fromParts(
            $expectedMajor,
            $expectedMinor,
            $expectedPatch,
            is_string($expectedPreRelease) ? PreRelease::fromIdentifiersString($expectedPreRelease) : $expectedPreRelease,
            is_string($expectedBuild) ? Build::fromIdentifiersString($expectedBuild) : $expectedBuild
        );
    }

    protected function matches($constraint) : bool
    {
        /* @var $constraint Version */

        return (
            $constraint->getMajor() === $this->expectedVersion->getMajor()
            && $constraint->getMinor() === $this->expectedVersion->getMinor()
            && $constraint->getPatch() === $this->expectedVersion->getPatch()
            && $constraint->getPreRelease()->getIdentifiers() === $this->expectedVersion->getPreRelease()->getIdentifiers()
            && $constraint->getBuild()->getIdentifiers() === $this->expectedVersion->getBuild()->getIdentifiers()
        );
    }

    public function toString() : string
    {
        return 'is identical to: ' . $this->expectedVersion->getVersionString();
    }
}
