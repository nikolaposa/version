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

use Version\Metadata\PreRelease;
use Version\Metadata\Build;
use Version\Exception\InvalidArgumentException;
use Version\Exception\InvalidVersionStringException;

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
     * @var PreRelease
     */
    private $preRelease = null;

    /**
     * @var Build
     */
    private $build = null;

    /**
     * @param int $major
     * @param int $minor
     * @param int $patch
     * @param PreRelease|array|string $preRelease OPTIONAL
     * @param Build|array|string $build OPTIONAL
     */
    public function __construct($major, $minor, $patch, $preRelease = null, $build = null)
    {
        if (!is_int($major) || $major < 0) {
            throw new InvalidArgumentException('Major version must be non-negative integer');
        }

        if (!is_int($minor) || $minor < 0) {
            throw new InvalidArgumentException('Minor version must be non-negative integer');
        }

        if (!is_int($patch) || $patch < 0) {
            throw new InvalidArgumentException('Patch version must be non-negative integer');
        }

        if ($preRelease !== null && !$preRelease instanceof PreRelease) {
            $preRelease = new PreRelease($preRelease);
        }

        if ($build !== null && !$build instanceof Build) {
            $build = new Build($build);
        }

        $this->major = $major;
        $this->minor = $minor;
        $this->patch = $patch;
        $this->preRelease = $preRelease;
        $this->build = $build;
    }

    /**
     * @param string $versionString
     * @return self
     * @throws InvalidArgumentException
     * @throws InvalidVersionStringException
     */
    public static function fromString($versionString)
    {
        if (!is_string($versionString)) {
            throw new InvalidArgumentException(__METHOD__ . ' expects a string');
        }

        $parts = [];

        if (!preg_match(
            '#^(?P<core>(?:[0-9]|[1-9][0-9]+)\.(?:[0-9]|[1-9][0-9]+)\.(?:[0-9]|[1-9][0-9]+))(?:\-(?P<preRelease>[0-9A-Za-z\-\.]+))?(?:\+(?P<build>[0-9A-Za-z\-\.]+))?$#',
            $versionString,
            $parts
        )) {
            throw new InvalidVersionStringException("Version string is not valid and cannot be parsed");
        }

        list($major, $minor, $patch) = explode('.', $parts['core']);
        $major = (int) $major;
        $minor = (int) $minor;
        $patch = (int) $patch;

        $preRelease = (!empty($parts['preRelease'])) ? $parts['preRelease'] : null;

        $build = (!empty($parts['build'])) ? $parts['build'] : null;

        return new self($major, $minor, $patch, $preRelease, $build);
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
     * @return PreRelease
     */
    public function getPreRelease()
    {
        return $this->preRelease;
    }

    /**
     * @return Build
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
            . (isset($this->preRelease) ? '-' . (string) $this->preRelease : '')
            . (isset($this->build) ? '+' . (string) $this->build : '')
        ;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getVersionString();
    }

    /**
     * @param self|string $version
     * @return int
     */
    private function compareTo($version)
    {
        if (!$version instanceof self) {
            $version = self::fromString((string) $version);
        }

        if ($this->major > $version->major) {
            return 1;
        }

        if ($this->major < $version->major) {
            return -1;
        }

        if ($this->minor > $version->minor) {
            return 1;
        }

        if ($this->minor < $version->minor) {
            return -1;
        }

        if ($this->patch > $version->patch) {
            return 1;
        }

        if ($this->patch < $version->patch) {
            return -1;
        }

        //... major, minor, and patch are equal, compare pre-releases

        if (!$this->preRelease && $version->preRelease) {
            // normal version has greater precedence than a pre-release version version
            return 1;
        }

        if ($this->preRelease && !$version->preRelease) {
            // pre-release version has lower precedence than a normal version
            return -1;
        }

        if ($this->preRelease && $version->preRelease) {
            return $this->preRelease->compareTo($version->preRelease);
        }

        // ... equal
        return 0;
    }

    /**
     * @param self|string $version
     * @return bool
     */
    public function isEqualTo($version)
    {
        return $this->compareTo($version) == 0;
    }

    /**
     * @param self|string $version
     * @return bool
     */
    public function isGreaterThan($version)
    {
        return $this->compareTo($version) > 0;
    }

    /**
     * @param self|string $version
     * @return bool
     */
    public function isLessThan($version)
    {
        return $this->compareTo($version) < 0;
    }

    /**
     * @param PreRelease|array|string $preRelease
     * @param Build|array|string $build
     * @return self
     */
    public function incrementMajor($preRelease = null, $build = null)
    {
        return new self($this->major + 1, 0, 0, $preRelease, $build);
    }

    /**
     * @param PreRelease|array|string $preRelease
     * @param Build|array|string $build
     * @return self
     */
    public function incrementMinor($preRelease = null, $build = null)
    {
        return new self($this->major, $this->minor + 1, 0, $preRelease, $build);
    }

    /**
     * @param PreRelease|array|string $preRelease
     * @param Build|array|string $build
     * @return self
     */
    public function incrementPatch($preRelease = null, $build = null)
    {
        return new self($this->major, $this->minor, $this->patch + 1, $preRelease, $build);
    }
}
