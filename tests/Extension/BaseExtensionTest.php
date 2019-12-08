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
    public function it_is_created_from_an_array_of_identifiers(): void
    {
        $extension = $this->createExtension(['123', '456']);

        $this->assertSame(['123', '456'], $extension->getIdentifiers());
    }

    /**
     * @test
     */
    public function it_can_be_created_from_string(): void
    {
        $extension = $this->createExtension('123.456');

        $this->assertSame(['123', '456'], $extension->getIdentifiers());
    }

    /**
     * @test
     */
    public function it_can_be_created_from_a_single_identifier_string(): void
    {
        $extension = $this->createExtension('123');

        $this->assertSame(['123'], $extension->getIdentifiers());
    }

    /**
     * @test
     */
    public function it_allows_for_checking_whether_it_is_empty(): void
    {
        $extension = $this->createExtension(['123']);

        $this->assertFalse($extension->isEmpty());
    }

    /**
     * @test
     */
    public function it_can_be_converted_to_string(): void
    {
        $extension = $this->createExtension(['123', '456']);

        $this->assertSame('123.456', (string) $extension);
    }

    /**
     * @test
     */
    public function it_raises_exception_in_case_of_an_invalid_identifier(): void
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
    public function it_raises_exception_in_case_of_an_empty_identifier(): void
    {
        try {
            $this->createExtension(['123', '']);

            $this->fail('Exception should have been raised');
        } catch (InvalidVersion $ex) {
            $this->assertStringContainsString('identifiers must include only alphanumerics and hyphen', $ex->getMessage());
        }
    }
}
