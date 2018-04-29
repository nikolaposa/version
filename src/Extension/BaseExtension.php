<?php

declare(strict_types=1);

namespace Version\Extension;

use Version\Exception\InvalidIdentifierException;

abstract class BaseExtension
{
    protected const IDENTIFIERS_SEPARATOR = '.';

    /**
     * @var array
     */
    private $identifiers;

    protected function __construct(string ...$identifiers)
    {
        foreach ($identifiers as $identifier) {
            $this->validate($identifier);
        }

        $this->identifiers = $identifiers;
    }

    /**
     * @param string $identifier
     * @throws InvalidIdentifierException
     * @return void
     */
    abstract protected function validate(string $identifier) : void;

    public static function fromIdentifiers(string $identifier, string ...$identifiers)
    {
        return new static(...array_merge([$identifier], $identifiers));
    }

    public static function fromIdentifiersString(string $identifiers)
    {
        return new static(...explode(self::IDENTIFIERS_SEPARATOR, $identifiers));
    }

    public function getIdentifiers() : array
    {
        return $this->identifiers;
    }

    public function isEmpty() : bool
    {
        return empty($this->identifiers);
    }

    public function __toString() : string
    {
        return implode(self::IDENTIFIERS_SEPARATOR, $this->identifiers);
    }
}
