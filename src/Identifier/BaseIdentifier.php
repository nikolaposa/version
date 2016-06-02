<?php

/**
 * This file is part of the Version package.
 *
 * Copyright (c) Nikola Posa <posa.nikola@gmail.com>
 *
 * For full copyright and license information, please refer to the LICENSE file,
 * located at the package root folder.
 */

namespace Version\Identifier;

use Version\Exception\InvalidIdentifierValueException;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
abstract class BaseIdentifier implements Identifier
{
    /**
     * @var string
     */
    private $value;

    private function __construct($value)
    {
        $this->value = $value;
    }

    public static function create($value)
    {
        if (!is_string($value)) {
            throw new InvalidIdentifierValueException('Identifier value must be of type string');
        }

        if ($value === '') {
            throw new InvalidIdentifierValueException('Identifier must not be empty');
        }

        static::validate($value);

        return new static($value);
    }

    /**
     * @param string $value
     * @throws InvalidIdentifierValueException
     */
    protected static function validate($value)
    {
    }

    public function getValue()
    {
        return $this->value;
    }

    public function __toString()
    {
        return $this->getValue();
    }
}
