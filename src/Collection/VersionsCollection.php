<?php
/**
 * This file is part of the Version package.
 *
 * Copyright (c) Nikola Posa <posa.nikola@gmail.com>
 *
 * For full copyright and license information, please refer to the LICENSE file,
 * located at the package root folder.
 */

namespace Version\Collection;

use Countable;
use IteratorAggregate;
use ArrayIterator;
use Version\Version;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
final class VersionsCollection implements Countable, IteratorAggregate
{
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
            if (!$version instanceof Version) {
                $version = Version::fromString((string) $version);
            }
            $this->versions[] = $version;
        }
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
     * @param bool $descending OPTIONAL
     * @return void
     */
    public function sort($descending = false)
    {
        usort($this->versions, function(Version $a, Version $b) use ($descending) {
            $result = $a->compareTo($b);

            if ($descending) {
                $result *= -1;
            }

            return $result;
        });
    }
}
