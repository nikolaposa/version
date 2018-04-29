<?php

declare(strict_types=1);

namespace Version\Tests\Metadata;

use Version\Metadata\PreRelease;
use Version\Identifier\PreReleaseIdentifier;
use Version\Exception\InvalidIdentifierValueException;
use Version\Exception\InvalidArgumentException;

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

    public function testExceptionIsRaisedInCaseOfInvalidIdentifiersArgument()
    {
        $this->expectException(InvalidArgumentException::class);

        PreRelease::create(false);
    }

    public function testExceptionIsRaisedInCaseOfInvalidIdentifier()
    {
        $this->expectException(InvalidIdentifierValueException::class);

        PreRelease::create([
            '_invalid#',
        ]);
    }

    public function testComparison()
    {
        $this->assertEquals(1, PreRelease::create('rc.2')->compareTo(PreRelease::create('rc.1')));
        $this->assertEquals(0, PreRelease::create('beta')->compareTo(PreRelease::create('beta')));
        $this->assertEquals(-1, PreRelease::create('beta1')->compareTo(PreRelease::create('beta2')));
        $this->assertEquals(1, PreRelease::create('beta')->compareTo(PreRelease::create('alpha')));
        $this->assertEquals(-1, PreRelease::create('beta.1')->compareTo(PreRelease::create('beta.test')));
    }
}
