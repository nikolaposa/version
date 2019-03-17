<?php

declare(strict_types=1);

namespace Version\Tests;

use PHPUnit\Framework\TestCase;
use Version\Version;

class VersionAwareTraitTest extends TestCase
{
    /**
     * @test
     */
    public function it_sets_version() : void
    {
        $object = $this->getObjectForTrait('\Version\VersionAwareTrait');
        $this->assertAttributeEquals(null, 'version', $object);

        $version = Version::fromParts(1);
        $object->setVersion($version);
        $this->assertAttributeEquals($version, 'version', $object);
    }

    /**
     * @test
     */
    public function it_gets_version() : void
    {
        $object = $this->getObjectForTrait('\Version\VersionAwareTrait');
        $this->assertNull($object->getVersion());

        $version = Version::fromParts(1);
        $object->setVersion($version);
        $this->assertSame($version, $object->getVersion());
    }
}
