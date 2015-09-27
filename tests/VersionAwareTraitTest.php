<?php
/**
 * This file is part of the Version package.
 *
 * Copyright (c) Nikola Posa <posa.nikola@gmail.com>
 *
 * For full copyright and license information, please refer to the LICENSE file,
 * located at the package root folder.
 */

namespace Version\Tests;

use Version\Version;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class VersionAwareTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testSetVersion()
    {
        $object = $this->getObjectForTrait('\Version\VersionAwareTrait');
        $this->assertAttributeEquals(null, 'version', $object);

        $version = new Version(1);
        $object->setVersion($version);
        $this->assertAttributeEquals($version, 'version', $object);
    }
    
    public function testGetVersion()
    {
        $object = $this->getObjectForTrait('\Version\VersionAwareTrait');
        $this->assertNull($object->getVersion());

        $version = new Version(1);
        $object->setVersion($version);
        $this->assertSame($version, $object->getVersion());
    }
}
