<?php

declare(strict_types=1);

namespace Version\Metadata;

use Version\Identifier\BuildIdentifier;
use Version\Identifier\Identifier;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
final class Build extends BaseIdentifyingMetadata
{
    protected static function createAssociatedIdentifier(string $value) : Identifier
    {
        return BuildIdentifier::create($value);
    }
}
