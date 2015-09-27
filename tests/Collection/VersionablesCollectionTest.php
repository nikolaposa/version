<?php
/**
 * This file is part of the Version package.
 *
 * Copyright (c) Nikola Posa <posa.nikola@gmail.com>
 *
 * For full copyright and license information, please refer to the LICENSE file,
 * located at the package root folder.
 */

namespace Version\Tests\Collection;

use Version\Collection\VersionablesCollection;
use Version\Version;
use Version\Tests\TestAsset\Package;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class VersionablesCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Version\Exception\InvalidArgumentException
     */
    public function testVersionablesCollectionAcceptsOnlyVersionableInstances()
    {
        new VersionablesCollection(['foobar']);
    }

    /**
     * @expectedException \Version\Exception\InvalidArgumentException
     */
    public function testExceptionInCaseOfObjectWithoutVersion()
    {
        $object1 = new Package('test');
        $object1->setVersion(new Version(1));

        $object2 = new Package('test');

        new VersionablesCollection([$object1, $object2]);
    }

    public function testCollectionCount()
    {
        $object1 = new Package('test');
        $object1->setVersion(new Version(1));

        $object2 = new Package('test');
        $object2->setVersion(new Version(2));

        $object3 = new Package('test');
        $object3->setVersion(new Version(3));

        $versionables = new VersionablesCollection([
            $object1,
            $object2,
            $object3,
        ]);

        $this->assertCount(3, $versionables);
    }

    public function testVersionsCollectionIteration()
    {
        $object1 = new Package('test');
        $object1->setVersion(new Version(1));

        $object2 = new Package('test');
        $object2->setVersion(new Version(2));

        $object3 = new Package('test');
        $object3->setVersion(new Version(3));

        $versionables = new VersionablesCollection([
            $object1,
            $object2,
            $object3,
        ]);

        foreach ($versionables as $object) {
            $this->assertInstanceOf('Version\VersionableInterface', $object);
        }
    }

    public function testVersionsCollectionSorting()
    {
        $ordered = [
            '2.3.0-alpha',
            '2.3.0',
        ];

        $object1 = new Package('test');
        $object1->setVersion(Version::fromString('2.3.0'));

        $object2 = new Package('test');
        $object2->setVersion(Version::fromString('2.3.0-alpha'));

        $versionables = new VersionablesCollection([
            $object1,
            $object2,
        ]);

        $versionables->sort();

        foreach ($versionables as $key => $object) {
            $this->assertEquals($ordered[$key], (string) $object->getVersion());
        }
    }

    public function testVersionsCollectionSortDescending()
    {
        $ordered = [
            '2.3.3',
            '2.3.0',
            '1.1.0',
        ];

        $object1 = new Package('test');
        $object1->setVersion(Version::fromString('1.1.0'));

        $object2 = new Package('test');
        $object2->setVersion(Version::fromString('2.3.3'));

        $object3 = new Package('test');
        $object3->setVersion(Version::fromString('2.3.0'));

        $versionables = new VersionablesCollection([
            $object1,
            $object2,
            $object3
        ]);

        $versionables->sort(true);

        foreach ($versionables as $key => $object) {
            $this->assertEquals($ordered[$key], (string) $object->getVersion());
        }
    }
}
