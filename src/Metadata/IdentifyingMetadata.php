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

use Version\Exception\InvalidArgumentException;
use Version\Identifier\Identifier;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
trait IdentifyingMetadata
{
    /**
     * @var Identifier[]
     */
    protected $identifiers;

    /**
     * @param array $identifiers
     */
    public function __construct(array $identifiers)
    {
        $this->validateIdentifiers($identifiers);

        $this->identifiers = $identifiers;
    }

    /**
     * @return string
     */
    abstract protected static function getIdentifierClass();

    /**
     * @param string $identifiersString
     * @return self
     * @throws InvalidArgumentException
     */
    public static function fromString($identifiersString)
    {
        $identifierClass = static::getIdentifierClass();

        if (strpos($identifiersString, '.') !== false) {
            $identifiers = [];

            $parts = explode('.', $identifiersString);
            foreach ($parts as $val) {
                if (empty($val)) {
                    throw new InvalidArgumentException('Identifiers must not be empty');
                }

                $identifiers[]= new $identifierClass($val);
            }
        } else {
            $identifiers = [new $identifierClass($identifiersString)];
        }

        return new self($identifiers);
    }

    /**
     * @param array $identifiers
     * @return void
     * @throws InvalidArgumentException
     */
    protected function validateIdentifiers(array $identifiers)
    {
        $identifierClass = static::getIdentifierClass();

        foreach ($identifiers as $identifier) {
            if (!$identifier instanceof $identifierClass) {
                throw new InvalidArgumentException("Identifier must be instance of $identifierClass");
            }
        }
    }

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
