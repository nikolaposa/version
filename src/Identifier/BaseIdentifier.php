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
    protected $value;

    /**
     * @param string $value
     */
    public function __construct($value)
    {
        $this->validate($value);

        $this->value = $value;
    }

    /**
     * @param string $value
     * @throws InvalidIdentifierValueException
     */
    protected function validate($value)
    {
        if (!is_string($value)) {
            throw new InvalidIdentifierValueException('Identifier value must be of type string');
        }

        if ($value === '') {
            throw new InvalidIdentifierValueException('Identifier must not be empty');
        }
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
