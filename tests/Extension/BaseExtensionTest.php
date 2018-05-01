<?php

declare(strict_types=1);

namespace Version\Tests\Extension;

use PHPUnit\Framework\TestCase;
use Version\Exception\InvalidExtensionIdentifierException;
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
        try {
            $this->createExtension(['$123']);

            $this->fail('Exception should have been raised');
        } catch (InvalidExtensionIdentifierException $ex) {
            $this->assertContains("identifier: '$123' is not valid; it must comprise only ASCII alphanumerics and hyphen", $ex->getMessage());
        }
    }

    public function testCreationFailsInCaseOfAnEmptyIdentifier()
    {
        try {
            $this->createExtension(['123', '']);

            $this->fail('Exception should have been raised');
        } catch (InvalidExtensionIdentifierException $ex) {
            $this->assertContains("identifier: '' is not valid; it must comprise only ASCII alphanumerics and hyphen", $ex->getMessage());
        }
    }
}
