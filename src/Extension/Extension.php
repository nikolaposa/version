<?php

declare(strict_types=1);

namespace Version\Extension;

use Version\Exception\InvalidVersion;

abstract class Extension
{
    protected const IDENTIFIERS_SEPARATOR = '.';

    private array $identifiers;

    final protected function __construct(array $identifiers)
    {
        $this->validate($identifiers);

        $this->identifiers = $identifiers;
    }

    /**
     * @throws InvalidVersion
     */
    abstract protected function validate(array $identifiers): void;

    public static function from(string $identifier, string ...$identifiers): static
    {
        return new static(func_get_args());
    }

    public static function fromArray(array $identifiers): static
    {
        return new static($identifiers);
    }

    public static function fromString(string $extension): static
    {
        return new static(explode(self::IDENTIFIERS_SEPARATOR, trim($extension)));
    }

    public function getIdentifiers(): array
    {
        return $this->identifiers;
    }

    public function toString(): string
    {
        return implode(self::IDENTIFIERS_SEPARATOR, $this->identifiers);
    }
}
