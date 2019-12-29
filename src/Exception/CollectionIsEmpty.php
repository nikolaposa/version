<?php

declare(strict_types=1);

namespace Version\Exception;

use OutOfBoundsException;

final class CollectionIsEmpty extends OutOfBoundsException implements VersionException
{
    public static function cannotGetFirst(): self
    {
        return new self('Cannot get the first Version from an empty collection');
    }

    public static function cannotGetLast(): self
    {
        return new self('Cannot get the last Version from an empty collection');
    }
}
