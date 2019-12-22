<?php

declare(strict_types=1);

namespace Version\Tests\Extension;

use PHPUnit\Framework\TestCase;
use Version\Exception\InvalidVersion;
use Version\Extension\Extension;

abstract class ExtensionTest extends TestCase
{
    /** @var string|Extension */
    protected $extensionClass;

    /**
     * @test
     */
    public function it_is_created_from_identifiers_list(): void
    {
        $extension = $this->extensionClass::from('123', '456');

        $this->assertSame(['123', '456'], $extension->getIdentifiers());
    }

    /**
     * @test
     */
    public function it_can_be_created_from_string(): void
    {
        $extension = $this->extensionClass::fromString('123.456');

        $this->assertSame(['123', '456'], $extension->getIdentifiers());
    }

    /**
     * @test
     */
    public function it_can_be_created_from_array(): void
    {
        $extension = $this->extensionClass::fromArray(['123', '456']);

        $this->assertSame(['123', '456'], $extension->getIdentifiers());
    }

    /**
     * @test
     */
    public function it_casts_to_string(): void
    {
        $extension = $this->extensionClass::from('123', '456');

        $this->assertSame('123.456', $extension->toString());
    }

    /**
     * @test
     */
    public function it_validates_identifier_input(): void
    {
        try {
            $this->extensionClass::from('$123');

            $this->fail('Exception should have been raised');
        } catch (InvalidVersion $ex) {
            $this->assertStringContainsString('identifiers can include only alphanumerics and hyphen', $ex->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_validates_empty_identifier_input(): void
    {
        try {
            $this->extensionClass::from('123', '');

            $this->fail('Exception should have been raised');
        } catch (InvalidVersion $ex) {
            $this->assertStringContainsString('identifiers can include only alphanumerics and hyphen', $ex->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_validates_empty_array_input(): void
    {
        try {
            $this->extensionClass::fromArray([]);

            $this->fail('Exception should have been raised');
        } catch (InvalidVersion $ex) {
            $this->assertStringContainsString('must contain at least one identifier', $ex->getMessage());
        }
    }
}
