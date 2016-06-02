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

use Version\Identifier\BuildIdentifier;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
final class Build extends BaseIdentifyingMetadata
{
    protected static function createAssociatedIdentifier($value)
    {
        return BuildIdentifier::create($value);
    }
}
