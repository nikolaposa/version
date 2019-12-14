<?php

declare(strict_types=1);

namespace Version\Extension;

use Version\Exception\InvalidVersion;

abstract class Extension
{
    protected const IDENTIFIERS_SEPARATOR = '.';

    /** @var array */
    private $identifiers;

    protected function __construct(string ...$identifiers)
    {
        foreach ($identifiers as $identifier) {
            $this->validate($identifier);
        }

        $this->identifiers = $identifiers;
    }

    public static function empty()
    {
        static $noExtension = null;

        if (null === $noExtension) {
            $noExtension = new static(...[]);
        }

        return $noExtension;
    }

    /**
     * @throws InvalidVersion
     */
    abstract protected function validate(string $identifier): void;

    public static function from(string $identifier, string ...$identifiers)
    {
        return new static($identifier, ...$identifiers);
    }

    public static function fromString(string $extension)
    {
        return new static(...explode(self::IDENTIFIERS_SEPARATOR, $extension));
    }

    public function getIdentifiers(): array
    {
        return $this->identifiers;
    }

    public function toString(): string
    {
        return implode(self::IDENTIFIERS_SEPARATOR, $this->identifiers);
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
