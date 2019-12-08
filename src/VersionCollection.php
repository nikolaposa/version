<?php

declare(strict_types=1);

namespace Version;

use Countable;
use IteratorAggregate;
use ArrayIterator;
use Traversable;
use Version\Comparison\Constraint\Constraint;

class VersionCollection implements Countable, IteratorAggregate
{
    /** @var Version[] */
    protected $versions;

    public function __construct(Version ...$versions)
    {
        $this->versions = $versions;
    }

    public function count(): int
    {
        return count($this->versions);
    }

    public function isEmpty(): bool
    {
        return empty($this->versions);
    }

    public function first(): ?Version
    {
        return $this->versions[0] ?? null;
    }

    public function last(): ?Version
    {
        return $this->versions[count($this->versions) - 1] ?? null;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->versions);
    }

    public function sortedAscending(): VersionCollection
    {
        $versions = $this->versions;

        usort($versions, function (Version $a, Version $b) {
            return $a->compareTo($b);
        });

        return new static(...$versions);
    }

    public function sortedDescending(): VersionCollection
    {
        $versions = $this->versions;

        usort($versions, function (Version $a, Version $b) {
            return $a->compareTo($b) * -1;
        });

        return new static(...$versions);
    }

    public function matching(Constraint $constraint): VersionCollection
    {
        return new static(...array_filter(
            $this->versions,
            function (Version $version) use ($constraint) {
                return $version->matches($constraint);
            }
        ));
    }

    public function majorReleases(): VersionCollection
    {
        return new static(...array_filter(
            $this->versions,
            function (Version $version) {
                return $version->isMajorRelease();
            }
        ));
    }

    public function minorReleases(): VersionCollection
    {
        return new static(...array_filter(
            $this->versions,
            function (Version $version) {
                return $version->isMinorRelease();
            }
        ));
    }

    public function patchReleases(): VersionCollection
    {
        return new static(...array_filter(
            $this->versions,
            function (Version $version) {
                return $version->isPatchRelease();
            }
        ));
    }

    /**
     * @return Version[]
     */
    public function toArray(): array
    {
        return $this->versions;
    }
}
