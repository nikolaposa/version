<?php
/**
 * This file is part of the Version package.
 *
 * Copyright (c) Nikola Posa <posa.nikola@gmail.com>
 *
 * For full copyright and license information, please refer to the LICENSE file,
 * located at the package root folder.
 */

namespace Version\Tests\TestAsset;

use Version\VersionableInterface;
use Version\VersionableTrait;
use Version\Collection\VersionsCollection;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class Package implements VersionableInterface
{
    use VersionableTrait;

    private $name;

    private $description;

    public function __construct($name, $description = '')
    {
        $this->name = $name;
        $this->description = $description;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getAvailableVersions()
    {
        return new VersionsCollection([
            '1.0.0-beta',
            '1.0.0',
            '1.0.1',
            '1.1.0',
            '1.2.0',
        ]);
    }
}
