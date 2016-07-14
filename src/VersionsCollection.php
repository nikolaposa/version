<?php

/**
 * This file is part of the Version package.
 *
 * Copyright (c) Nikola Posa <posa.nikola@gmail.com>
 *
 * For full copyright and license information, please refer to the LICENSE file,
 * located at the package root folder.
 */

namespace Version;

use Countable;
use IteratorAggregate;
use ArrayIterator;
use Version\Exception\InvalidArgumentException;
use Version\Constraint\ConstraintInterface;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
final class VersionsCollection implements Countable, IteratorAggregate
{
    const SORT_ASC = 'ASC';
    const SORT_DESC = 'DESC';

    /**
     * @var Version[]
     */
    private $versions = [];

    /**
     * @param array $versions
     */
    public function __construct(array $versions)
    {
        foreach ($versions as $version) {
            if (is_string($version)) {
                $version = Version::fromString($version);
            } elseif (!$version instanceof Version) {
                throw new InvalidArgumentException(sprintf(
                    'Item in the versions array should be either string or Version instance, %s given',
                    gettype($version)
                ));
            }

            $this->versions[] = $version;
        }
    }

    /**
     * @param array $versions
     * @return self
     */
    public static function fromArray(array $versions)
    {
        return new self($versions);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->versions);
    }

    /**
     * @return \Iterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->versions);
    }

    /**
     * @param string|bool $direction OPTIONAL
     * @return void
     */
    public function sort($direction = self::SORT_ASC)
    {
        if (is_bool($direction)) {
            //backwards-compatibility
            $direction = (true === $direction) ? self::SORT_DESC : self::SORT_ASC;
        }

        usort($this->versions, function (Version $a, Version $b) use ($direction) {
            $result = $a->compareTo($b);

            if ($direction == self::SORT_DESC) {
                $result *= -1;
            }

            return $result;
        });
    }

    public function matching(ConstraintInterface $constraint)
    {
        return new self(array_filter(
            $this->versions,
            function (Version $version) use ($constraint) {
                return $version->matches($constraint);
            }
        ));
    }
}
