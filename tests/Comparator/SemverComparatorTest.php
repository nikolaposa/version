<?php

/**
 * This file is part of the Version package.
 *
 * Copyright (c) Nikola Posa <posa.nikola@gmail.com>
 *
 * For full copyright and license information, please refer to the LICENSE file,
 * located at the package root folder.
 */

namespace Version\Tests\Comparator;

use PHPUnit_Framework_TestCase;
use Version\Comparator\SemverComparator;
use Version\Version;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class SemverComparatorTest extends PHPUnit_Framework_TestCase
{
    protected $comparator;

    protected function setUp()
    {
        $this->comparator = new SemverComparator();
    }

    /**
     * @dataProvider versionsCompareList
     */
    public function testComparison($version1String, $version2String, $result)
    {
        $this->assertEquals(
            $result,
            $this->comparator->compare(
                Version::fromString($version1String),
                Version::fromString($version2String)
            )
        );
    }

    public static function versionsCompareList()
    {
        return [
            ['2.1.1', '2.1.0', 1],
            ['1.10.1', '2.1.0', -1],
            ['1.0.0', '1.0.0', 0],
            ['1.0.0', '1.0.1', -1],
            ['1.0.0', '1.0.0-alpha', 1],
            ['1.0.0-alpha', '1.0.0-beta', -1],
            ['1.0.0-alpha.1', '1.0.0-alpha', 1],
            ['1.0.0-alpha.1', '1.0.0-alpha', 1],
            ['1.0.0-alpha.beta', '1.0.0-alpha.1', 1],
            ['1.0.0-beta', '1.0.0-alpha.beta', 1],
            ['1.0.0-beta.11', '1.0.0-beta.2', 1],
            ['1.0.0-rc.1', '1.0.0-beta.11', 1],
            ['1.0.0-rc.1.1', '1.0.0-rc.1', 1],
            ['1.0.0', '1.0.0-rc.1', 1],
            ['1.0.0-alpha+20150919', '1.0.0-alpha+exp.sha.5114f85', 0],
        ];
    }
}
