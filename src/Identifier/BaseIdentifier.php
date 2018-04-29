<?php

declare(strict_types=1);

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

    protected function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @param string $value
     * @return static
     * @throws InvalidIdentifierValueException
     */
    public static function create(string $value) : Identifier
    {
        if ('' === $value) {
            throw new InvalidIdentifierValueException('Identifier must not be empty');
        }

        static::validate($value);

        return new static($value);
    }

    /**
     * @param string $value
     * @throws InvalidIdentifierValueException
     */
    protected static function validate(string $value) : void
    {
    }

    public function getValue() : string
    {
        return $this->value;
    }

    public function __toString() : string
    {
        return $this->getValue();
    }
}
