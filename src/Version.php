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

use Version\Identifier\PreRelease;
use Version\Identifier\Build;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
final class Version
{
    /**
     * @var int
     */
    private $major;

    /**
     * @var int
     */
    private $minor;

    /**
     * @var int
     */
    private $patch;

    /**
     * @var PreRelease[]
     */
    private $preRelease = array();

    /**
     * @var Build[]
     */
    private $build = array();

    /**
     * Force usage of factory methods.
     */
    private function __construct()
    {
    }

    /**
     * @param string $versionString
     * @return self
     * @throws Exception\InvalidArgumentException
     */
    public static function fromString($versionString)
    {
        $parts = array();

        if (!preg_match(
            '#^(?P<core>(?:[0-9]|[1-9][0-9]+)\.(?:[0-9]|[1-9][0-9]+)\.(?:[0-9]|[1-9][0-9]+))(?:\-(?P<preRelease>[0-9A-Za-z\-\.]+))?(?:\+(?P<build>[0-9A-Za-z\-\.]+))?$#',
            $versionString,
            $parts
        )) {
            throw new Exception\InvalidVersionStringException("Version string is not valid and cannot be parsed");
        }

        $version = new self();

        list($major, $minor, $patch) = explode('.', $parts['core']);

        $version->major = $major;
        $version->minor = $minor;
        $version->patch = $patch;

        if (!empty($parts['preRelease'])) {
            if (strpos($parts['preRelease'], '.') !== false) {
                $preRelease = array();

                $preReleaseParts = explode('.', $parts['preRelease']);
                foreach ($preReleaseParts as $preReleaseVal) {
                    $preRelease[]= new PreRelease($preReleaseVal);
                }
            } else {
                $preRelease = array(new PreRelease($parts['preRelease']));
            }

            $version->preRelease = $preRelease;
        }

        if (!empty($parts['build'])) {
            if (strpos($parts['build'], '.') !== false) {
                $build = array();

                $buildParts = explode('.', $parts['build']);
                foreach ($buildParts as $buildVal) {
                    $build[]= new Build($buildVal);
                }
            } else {
                $build = array(new Build($buildVal));
            }

            $version->build = $build;
        }

        return $version;
    }

    /**
     * @return int
     */
    public function getMajor()
    {
        return $this->major;
    }

    /**
     * @return int
     */
    public function getMinor()
    {
        return $this->minor;
    }

    /**
     * @return int
     */
    public function getPatch()
    {
        return $this->patch;
    }

    /**
     * @return PreRelease[]
     */
    public function getPreRelease()
    {
        return $this->preRelease;
    }

    /**
     * @return Build[]
     */
    public function getBuild()
    {
        return $this->build;
    }

    /**
     * @return string
     */
    public function getVersionString()
    {
        return
            $this->major
            . '.' . $this->minor
            . '.' . $this->patch
            . (!empty($this->preRelease) ? '-' . implode('.', $this->preRelease) : '')
            . (!empty($this->build) ? '+' . implode('.', $this->build) : '')
        ;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getVersionString();
    }
}
