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

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
final class PreRelease
{
    use IdentifyingMetadata;

    protected static function getIdentifierClass()
    {
        return 'Version\Identifier\PreRelease';
    }
}
