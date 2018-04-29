<?php

declare(strict_types=1);

namespace Version\Tests\Extension;

use PHPUnit\Framework\TestCase;
use Version\Exception\InvalidIdentifierException;
use Version\Extension\BaseExtension;

abstract class BaseExtensionTest extends TestCase
{
    abstract protected function createExtension($identifiers) : BaseExtension;

    public function testCreatingFromIdentifiers()
    {
        $extension = $this->createExtension(['123', '456']);

        $this->assertSame(['123', '456'], $extension->getIdentifiers());
    }

    public function testCreatingFromString()
    {
        $extension = $this->createExtension('123.456');

        $this->assertSame(['123', '456'], $extension->getIdentifiers());
    }

    public function testCreatingFromSingleIdentifierString()
    {
        $extension = $this->createExtension('123');

        $this->assertSame(['123'], $extension->getIdentifiers());
    }

    public function testCheckingIsEmpty()
    {
        $extension = $this->createExtension(['123']);

        $this->assertFalse($extension->isEmpty());
    }

    public function testToStringConversion()
    {
        $extension = $this->createExtension(['123', '456']);

        $this->assertEquals('123.456', (string) $extension);
    }

    public function testExceptionIsRaisedInCaseOfInvalidIdentifier()
    {
        $this->expectException(InvalidIdentifierException::class);

        $this->createExtension(['$123']);
    }
}
