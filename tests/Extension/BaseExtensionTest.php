<?php

declare(strict_types=1);

namespace Version\Tests\Extension;

use PHPUnit\Framework\TestCase;
use Version\Exception\InvalidVersion;
use Version\Extension\BaseExtension;

abstract class BaseExtensionTest extends TestCase
{
    abstract protected function createExtension($identifiers): BaseExtension;

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
    public function it_checks_for_emptiness(): void
    {
        $extension = $this->createExtension(['123']);

        $this->assertFalse($extension->isEmpty());
    }

    /**
     * @test
     */
    public function it_casts_to_string(): void
    {
        $extension = $this->createExtension(['123', '456']);

        $this->assertSame('123.456', (string) $extension);
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
