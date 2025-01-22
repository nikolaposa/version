<?php

    declare(strict_types=1);

    namespace Version\Tests\TestAsset;

    use PHPUnit\Framework\Constraint\Constraint;
    use Version\Extension\Build;
    use Version\Extension\PreRelease;
    use Version\Version;

    final class VersionIsIdentical extends Constraint
    {
        private Version $expectedVersion;


        public function __construct(
            int $expectedMajor,
            int $expectedMinor,
            int $expectedPatch,
            PreRelease|string|null $expectedPreRelease = NULL,
            Build|string|null $expectedBuild = NULL
        ) {
            $this->expectedVersion = Version::from(
                $expectedMajor,
                $expectedMinor,
                $expectedPatch,
                is_string($expectedPreRelease) ? PreRelease::fromString($expectedPreRelease) : $expectedPreRelease,
                is_string($expectedBuild) ? Build::fromString($expectedBuild) : $expectedBuild
            );
        }


        protected function matches($version): bool
        {
            /* @var $version Version */

            return (
                $version->getMajor() === $this->expectedVersion->getMajor()
                && $version->getMinor() === $this->expectedVersion->getMinor()
                && $version->getPatch() === $this->expectedVersion->getPatch()
                && (
                    ($version->isPreRelease() && $this->expectedVersion->isPreRelease() && $version->getPreRelease()->getIdentifiers(
                        ) === $this->expectedVersion->getPreRelease()->getIdentifiers())
                    || (!$version->isPreRelease() && !$this->expectedVersion->isPreRelease())
                )
                && (
                    ($version->hasBuild() && $this->expectedVersion->hasBuild() && $version->getBuild()->getIdentifiers() === $this->expectedVersion->getBuild()
                                                                                                                                                    ->getIdentifiers(
                                                                                                                                                    ))
                    || (!$version->hasBuild() && !$this->expectedVersion->hasBuild())
                )
            );
        }


        public function toString(): string
        {
            return 'is identical to: '.$this->expectedVersion->toString();
        }
    }
