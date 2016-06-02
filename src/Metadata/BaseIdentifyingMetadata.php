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

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
abstract class BaseIdentifyingMetadata
{
    /**
     * @var Identifier[]
     */
    private $identifiers;

    private function __construct($identifiers)
    {
        $this->identifiers = $identifiers;
    }

    /**
     * @param array|string $identifiers
     * @return self
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

    private static function createIdentifier($identifier)
    {
        $identifierClass = static::getIdentifierClass();

        if ($identifier instanceof $identifierClass) {
            return $identifier;
        }

        return call_user_func([$identifierClass, 'create'], $identifier);
    }

    /**
     * @return string
     */
    abstract protected static function getIdentifierClass();

    /**
     * @return array
     */
    public function getIdentifiers()
    {
        return $this->identifiers;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode('.', $this->getIdentifiers());
    }
}
