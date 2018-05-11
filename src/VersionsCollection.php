<?php

declare(strict_types=1);

namespace Version;

use Countable;
use IteratorAggregate;
use ArrayIterator;
use Traversable;
use Version\Constraint\ConstraintInterface;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class VersionsCollection implements Countable, IteratorAggregate
{
    public const SORT_ASC = 'ASC';
    public const SORT_DESC = 'DESC';

    /**
     * @var Version[]
     */
    protected $versions;

    public function __construct(Version ...$versions)
    {
        $this->versions = $versions;
    }

    public function count() : int
    {
        return count($this->versions);
    }

    public function isEmpty() : bool
    {
        return empty($this->versions);
    }

    public function getIterator() : Traversable
    {
        return new ArrayIterator($this->versions);
    }

    /**
     * @deprecated This method will be removed in 4.0.0. Use sorted() instead.
     */
    public function sort(string $direction = self::SORT_ASC) : void
    {
        usort($this->versions, function (Version $a, Version $b) use ($direction) {
            $result = $a->compareTo($b);

            if ($direction === self::SORT_DESC) {
                $result *= -1;
            }

            return $result;
        });
    }

    public function sorted(string $direction = self::SORT_ASC) : VersionsCollection
    {
        $versions = $this->versions;

        usort($versions, function (Version $a, Version $b) use ($direction) {
            $result = $a->compareTo($b);

            if ($direction === self::SORT_DESC) {
                $result *= -1;
            }

            return $result;
        });

        return new static(...$versions);
    }

    public function matching(ConstraintInterface $constraint) : VersionsCollection
    {
        return new static(...array_filter(
            $this->versions,
            function (Version $version) use ($constraint) {
                return $version->matches($constraint);
            }
        ));
    }

    /**
     * @return Version[]
     */
    public function toArray() : array
    {
        return $this->versions;
    }
}
