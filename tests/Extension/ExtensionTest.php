<?php

declare(strict_types=1);

namespace Version\Tests\Extension;

use PHPUnit\Framework\TestCase;
use Version\Exception\InvalidVersion;
use Version\Extension\Extension;

abstract class ExtensionTest extends TestCase
{
    abstract protected function createExtension($identifiers): Extension;

    /**
     * @test
     */
    public function it_is_created_from_identifiers_array(): void
    {
        $extension = $this->createExtension(['123', '456']);

        $this->assertSame(['123', '456'], $extension->getIdentifiers());
    }

    /**
     * @test
     */
    public function it_can_be_created_from_string(): void
    {
        $extension = $this->createExtension('123');

        $this->assertSame(['123'], $extension->getIdentifiers());
    }

    /**
     * @test
     */
    public function it_can_be_created_from_compound_string(): void
    {
        $extension = $this->createExtension('123.456');

        $this->assertSame(['123', '456'], $extension->getIdentifiers());
    }

    /**
     * @test
     */
    public function it_casts_to_string(): void
    {
        $extension = $this->createExtension(['123', '456']);

        $this->assertSame('123.456', $extension->toString());
    }

    /**
     * @test
     */
    public function it_validates_identifier_input(): void
    {
        try {
            $this->createExtension(['$123']);

            $this->fail('Exception should have been raised');
        } catch (InvalidVersion $ex) {
            $this->assertStringContainsString('identifiers must include only alphanumerics and hyphen', $ex->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_validates_empty_identifier_input(): void
    {
        try {
            $this->createExtension(['123', '']);

            $this->fail('Exception should have been raised');
        } catch (InvalidVersion $ex) {
            $this->assertStringContainsString('identifiers must include only alphanumerics and hyphen', $ex->getMessage());
        }
    }
}
