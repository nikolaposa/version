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

use Version\Exception\InvalidArgumentException;
use Version\Exception\InvalidIdentifierValueException;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
abstract class BaseIdentifier
{
    /**
     * @var string
     */
    protected $value;

    /**
     * @param string $value
     */
    public function __construct($value)
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException(__CLASS__ . ' value must be of type string');
        }

        if (!preg_match('/^[[:alnum]]+$/', $value)) {
            throw new InvalidIdentifierValueException(__CLASS__ . ' value must contain only alphanumeric characters');
        }

        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getValue();
    }
}
