<?php

declare(strict_types=1);

namespace Version\Tests\TestAsset;

use PHPUnit\Framework\Constraint\Constraint;
use Version\Extension\Build;
use Version\Extension\PreRelease;
use Version\Version;

final class VersionIsIdentical extends Constraint
{
    /** @var Version */
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

    protected function matches($version) : bool
    {
        /* @var $version Version */

        return (
            $version->getMajor() === $this->expectedVersion->getMajor()
            && $version->getMinor() === $this->expectedVersion->getMinor()
            && $version->getPatch() === $this->expectedVersion->getPatch()
            && $version->getPreRelease()->getIdentifiers() === $this->expectedVersion->getPreRelease()->getIdentifiers()
            && $version->getBuild()->getIdentifiers() === $this->expectedVersion->getBuild()->getIdentifiers()
        );
    }

    public function toString() : string
    {
        return 'is identical to: ' . $this->expectedVersion->getVersionString();
    }
}
