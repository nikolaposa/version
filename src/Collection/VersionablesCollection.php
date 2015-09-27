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
use Version\VersionableInterface;
use Version\Exception\InvalidArgumentException;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
final class VersionablesCollection implements Countable, IteratorAggregate
{
    /**
     * @var VersionableInterface[]
     */
    private $versionables = [];

    /**
     * @param array $versionables
     */
    public function __construct(array $versionables)
    {
        foreach ($versionables as $object) {
            if (!$object instanceof VersionableInterface) {
                throw new InvalidArgumentException(__METHOD__ . ' expects a array of VersionableInterface objects');
            }

            if (null === $object->getVersion()) {
                throw new InvalidArgumentException('Versionable object has no version');
            }

            $this->versionables[] = $object;
        }
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->versionables);
    }

    /**
     * @return \Iterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->versionables);
    }

    /**
     * @param bool $descending OPTIONAL
     * @return void
     */
    public function sort($descending = false)
    {
        usort($this->versionables, function(VersionableInterface $a, VersionableInterface $b) use ($descending) {
            $result = $a->getVersion()->compareTo($b->getVersion());

            if ($descending) {
                $result *= -1;
            }

            return $result;
        });
    }
}
