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

use Version\Metadata\PreRelease;
use Version\Identifier\PreReleaseIdentifier;
use Version\Exception\InvalidIdentifierValueException;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class PreReleaseTest extends BaseMetadataTest
{
    public function testCreatingFromArray()
    {
        $preRelese = PreRelease::create([
            'alpha',
            PreReleaseIdentifier::create('1'),
        ]);

        $this->assertMetadata(['alpha', '1'], $preRelese);
    }

    public function testCreatingFromString()
    {
        $preRelese = PreRelease::create('rc.2');

        $this->assertMetadata(['rc', '2'], $preRelese);
    }

    public function testCreatingFromSinglePartString()
    {
        $preRelese = PreRelease::create('beta');

        $this->assertMetadata(['beta'], $preRelese);
    }

    public function testCreatingEmpty()
    {
        $preRelese = PreRelease::createEmpty();

        $this->assertMetadata([], $preRelese);
        $this->assertTrue($preRelese->isEmpty());
    }

    public function testToArrayConversion()
    {
        $preRelese = PreRelease::create('rc.2');

        $this->assertEquals(['rc', '2'], $preRelese->toArray());
    }

    public function testToStringConversion()
    {
        $preRelese = PreRelease::create([
            'rc',
            '2',
        ]);

        $this->assertEquals('rc.2', (string) $preRelese);
    }

    public function testExceptionIsRaisedInCaseOfInvalidIdentifier()
    {
        $this->setExpectedException(InvalidIdentifierValueException::class);

        PreRelease::create([
            '_invalid#',
        ]);
    }
}
