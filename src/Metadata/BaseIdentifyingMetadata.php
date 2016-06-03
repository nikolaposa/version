<?php

/**
 * This file is part of the Version package.
 *
 * Copyright (c) Nikola Posa <posa.nikola@gmail.com>
 *
 * For full copyright and license information, please refer to the LICENSE file,
 * located at the package root folder.
 */

namespace Version\Metadata;

use Version\Identifier\Identifier;
use Version\Exception\InvalidArgumentException;
use Version\Exception\LogicException;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
abstract class BaseIdentifyingMetadata
{
    /**
     * @var Identifier[]
     */
    private $identifiers;

    private function __construct(array $identifiers = [])
    {
        $this->identifiers = $identifiers;
    }

    /**
     * @param array|string $identifiers
     * @return static
     * @throws InvalidArgumentException
     */
    public static function create($identifiers)
    {
        if (is_array($identifiers)) {
            return self::createFromArray($identifiers);
        } elseif (is_string($identifiers)) {
            return self::createFromString($identifiers);
        } else {
            throw new InvalidArgumentException('Identifiers parameter should be either array or string');
        }
    }

    private static function createFromArray(array $identifiersArray)
    {
        $identifiers = [];

        foreach ($identifiersArray as $id) {
            $identifiers[]= self::createIdentifier($id);
        }

        return new static($identifiers);
    }

    private static function createFromString($identifiersString)
    {
        if (strpos($identifiersString, '.') !== false) {
            $identifiers = [];

            $ids = explode('.', $identifiersString);

            foreach ($ids as $id) {
                $identifiers[]= self::createIdentifier($id);
            }

            return new static($identifiers);
        }

        return new static([self::createIdentifier($identifiersString)]);
    }

    private static function createIdentifier($value)
    {
        if ($value instanceof Identifier) {
            return $value;
        }

        return static::createAssociatedIdentifier($value);
    }

    /**
     * @return static
     */
    public static function createEmpty()
    {
        return new static([]);
    }

    /**
     * @param string $value
     * @return Identifier
     */
    protected static function createAssociatedIdentifier($value)
    {
        throw new LogicException(__METHOD__ . ' not implemented');
    }

    /**
     * @return array
     */
    public function getIdentifiers()
    {
        return $this->identifiers;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->identifiers);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_map(function ($identifier) {
            return $identifier->getValue();
        }, $this->identifiers);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode('.', $this->getIdentifiers());
    }
}
