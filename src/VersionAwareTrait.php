<?php
/**
 * This file is part of the Version package.
 *
 * Copyright (c) Nikola Posa <posa.nikola@gmail.com>
 *
 * For full copyright and license information, please refer to the LICENSE file,
 * located at the package root folder.
 */

namespace Version;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
trait VersionAwareTrait
{
    /**
     * @var Version
     */
    protected $version;

    /**
     * @param Version $version
     * @return void
     */
    public function setVersion(Version $version)
    {
        $this->version = $version;
    }

    /**
     * @return Version
     */
    public function getVersion()
    {
        return $this->version;
    }
}
