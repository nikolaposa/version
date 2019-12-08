<?php

declare(strict_types=1);

namespace Version;

use Countable;
use IteratorAggregate;
use ArrayIterator;
use Traversable;
use Version\Comparison\Constraint\Constraint;
use Version\Exception\CollectionIsEmptyException;

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

    public function first(): Version
    {
        if (empty($this->versions)) {
            throw new CollectionIsEmptyException('Invoking first() on an empty collection');
        }

        return $this->versions[0];
    }

    public function last(): Version
    {
        if (empty($this->versions)) {
            throw new CollectionIsEmptyException('Invoking last() on an empty collection');
        }

        return $this->versions[count($this->versions) - 1];
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->versions);
    }

    /**
     * @deprecated This method will be removed in 4.0.0. Use sorted() instead.
     */
    public function sort(string $direction = self::SORT_ASC): void
    {
        usort($this->versions, function (Version $a, Version $b) use ($direction) {
            $result = $a->compareTo($b);

            if ($direction === self::SORT_DESC) {
                $result *= -1;
            }

            return $result;
        });
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
