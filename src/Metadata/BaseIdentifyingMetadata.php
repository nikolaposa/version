<?php

declare(strict_types=1);

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

    private function __construct(Identifier ...$identifiers)
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
        if ($identifiers instanceof self) {
            return $identifiers;
        }

        if (is_array($identifiers)) {
            return self::createFromArray($identifiers);
        }

        if (is_string($identifiers)) {
            return self::createFromString($identifiers);
        }

        throw new InvalidArgumentException('Identifiers parameter should be either array or string');
    }

    private static function createFromArray(array $identifiersArray)
    {
        $identifiers = [];

        foreach ($identifiersArray as $id) {
            $identifiers[]= self::createIdentifier($id);
        }

        return new static(...$identifiers);
    }

    private static function createFromString(string $identifiersString)
    {
        if (strpos($identifiersString, '.') !== false) {
            $identifiers = [];

            $ids = explode('.', $identifiersString);

            foreach ($ids as $id) {
                $identifiers[]= self::createIdentifier($id);
            }

            return new static(...$identifiers);
        }

        return new static(...[self::createIdentifier($identifiersString)]);
    }

    protected static function createIdentifier($value) : Identifier
    {
        if ($value instanceof Identifier) {
            return $value;
        }

        return static::createAssociatedIdentifier($value);
    }

    public static function createEmpty()
    {
        return new static();
    }

    protected static function createAssociatedIdentifier(string $value) : Identifier
    {
        throw new LogicException(__METHOD__ . ' not implemented');
    }

    public function getIdentifiers() : array
    {
        return $this->identifiers;
    }

    public function isEmpty() : bool
    {
        return empty($this->identifiers);
    }

    public function toArray() : array
    {
        return array_map(function (Identifier $identifier) {
            return $identifier->getValue();
        }, $this->identifiers);
    }

    public function __toString() : string
    {
        return implode('.', $this->getIdentifiers());
    }
}
