<?php

declare(strict_types=1);

namespace Version\Tests\Metadata;

use PHPUnit\Framework\TestCase;
use Version\Metadata\BaseIdentifyingMetadata;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
abstract class BaseMetadataTest extends TestCase
{
    public static function assertMetadata($identifiers, BaseIdentifyingMetadata $metadata)
    {
        $actualIdentifiers = $metadata->getIdentifiers();

        self::assertCount(count($identifiers), $metadata->getIdentifiers());

        foreach ($actualIdentifiers as $i => $identifier) {
            /* @var $identifier \Version\Identifier\Identifier */
            self::assertEquals($identifiers[$i], $identifier->getValue());
        }
    }
}
