<?php

/**
 * This file is part of the Version package.
 *
 * Copyright (c) Nikola Posa <posa.nikola@gmail.com>
 *
 * For full copyright and license information, please refer to the LICENSE file,
 * located at the package root folder.
 */

namespace Version\Tests\Metadata;

use PHPUnit_Framework_TestCase;
use Version\Metadata\BaseIdentifyingMetadata;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
abstract class BaseMetadataTest extends PHPUnit_Framework_TestCase
{
    public static function assertMetadata($identifiers, BaseIdentifyingMetadata $metadata)
    {
        $actualIdentifiers = $metadata->getIdentifiers();

        self::assertCount(count($identifiers), $metadata->getIdentifiers());

        foreach ($actualIdentifiers as $i => $identifier) {
            self::assertEquals($identifiers[$i], $identifier->getValue());
        }
    }
}
