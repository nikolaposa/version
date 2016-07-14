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

use Version\Metadata\Build;
use Version\Identifier\BuildIdentifier;
use Version\Exception\InvalidIdentifierValueException;
use Version\Exception\InvalidArgumentException;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class BuildTest extends BaseMetadataTest
{
    public function testCreatingFromArray()
    {
        $build = Build::create([
            '123',
            BuildIdentifier::create('456'),
        ]);

        $this->assertMetadata(['123', '456'], $build);
    }

    public function testCreatingFromString()
    {
        $build = Build::create('123.456');

        $this->assertMetadata(['123', '456'], $build);
    }

    public function testCreatingFromSinglePartString()
    {
        $build = Build::create('123');

        $this->assertMetadata(['123'], $build);
    }

    public function testCreatingEmpty()
    {
        $build = Build::createEmpty();

        $this->assertMetadata([], $build);
        $this->assertTrue($build->isEmpty());
    }

    public function testToArrayConversion()
    {
        $build = Build::create('123.456');

        $this->assertEquals(['123', '456'], $build->toArray());
    }

    public function testToStringConversion()
    {
        $build = Build::create([
            '123',
            '456',
        ]);

        $this->assertEquals('123.456', (string) $build);
    }

    public function testExceptionIsRaisedInCaseOfInvalidIdentifiersArgument()
    {
        $this->setExpectedException(InvalidArgumentException::class);

        Build::create(false);
    }

    public function testExceptionIsRaisedInCaseOfInvalidIdentifier()
    {
        $this->setExpectedException(InvalidIdentifierValueException::class);

        Build::create([
            '_invalid#',
        ]);
    }

    public function testExceptionIsRaisedInCaseOfInvalidIdentifierType()
    {
        $this->setExpectedException(
            InvalidIdentifierValueException::class,
            'Identifier value must be of type string'
        );

        Build::create([
            123,
        ]);
    }
}
