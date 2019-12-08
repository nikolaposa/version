<?php

declare(strict_types=1);

namespace Version;

use Countable;
use IteratorAggregate;
use ArrayIterator;
use Traversable;
use Version\Comparison\Constraint\Constraint;

class VersionsCollection implements Countable, IteratorAggregate
{
    public const SORT_ASC = 'ASC';
    public const SORT_DESC = 'DESC';

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

    public function sortedAscending(): VersionsCollection
    {
        $versions = $this->versions;

        usort($versions, function (Version $a, Version $b) {
            return $a->compareTo($b);
        });

        return new static(...$versions);
    }

    public function sortedDescending(): VersionsCollection
    {
        $versions = $this->versions;

        usort($versions, function (Version $a, Version $b) {
            return $a->compareTo($b) * -1;
        });

        return new static(...$versions);
    }

    public function matching(Constraint $constraint): VersionsCollection
    {
        return new static(...array_filter(
            $this->versions,
            function (Version $version) use ($constraint) {
                return $version->matches($constraint);
            }
        ));
    }

    public function majorReleases(): VersionsCollection
    {
        return new static(...array_filter(
            $this->versions,
            function (Version $version) {
                return $version->isMajorRelease();
            }
        ));
    }

    public function minorReleases(): VersionsCollection
    {
        return new static(...array_filter(
            $this->versions,
            function (Version $version) {
                return $version->isMinorRelease();
            }
        ));
    }

    public function patchReleases(): VersionsCollection
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
