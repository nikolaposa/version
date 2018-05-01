<?php

declare(strict_types=1);

namespace Version\Tests;

use PHPUnit\Framework\TestCase;
use Version\Version;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class VersionAwareTraitTest extends TestCase
{
    public function testSetVersion()
    {
        $object = $this->getObjectForTrait('\Version\VersionAwareTrait');
        $this->assertAttributeEquals(null, 'version', $object);

        $version = Version::fromParts(1);
        $object->setVersion($version);
        $this->assertAttributeEquals($version, 'version', $object);
    }

    public function testGetVersion()
    {
        $object = $this->getObjectForTrait('\Version\VersionAwareTrait');
        $this->assertNull($object->getVersion());

        $version = Version::fromParts(1);
        $object->setVersion($version);
        $this->assertSame($version, $object->getVersion());
    }
}
