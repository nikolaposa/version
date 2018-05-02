<?php

declare(strict_types=1);

namespace Version;

use JsonSerializable;
use Version\Extension\Build;
use Version\Extension\NoBuild;
use Version\Extension\NoPreRelease;
use Version\Exception\InvalidVersionException;
use Version\Exception\InvalidVersionStringException;
use Version\Comparator\ComparatorInterface;
use Version\Comparator\SemverComparator;
use Version\Constraint\ConstraintInterface;
use Version\Extension\PreRelease;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class Version implements JsonSerializable
{
    /**
     * @var int
     */
    protected $major;

    /**
     * @var int
     */
    protected $minor;

    /**
     * @var int
     */
    protected $patch;

    /**
     * @var PreRelease
     */
    protected $preRelease;

    /**
     * @var Build
     */
    protected $build;

    protected function __construct(int $major, int $minor, int $patch, PreRelease $preRelease, Build $build)
    {
        $this->validateNumber('major', $major);
        $this->validateNumber('minor', $minor);
        $this->validateNumber('patch', $patch);

        $this->major = $major;
        $this->minor = $minor;
        $this->patch = $patch;
        $this->preRelease = $preRelease;
        $this->build = $build;
    }

    protected function validateNumber(string $name, int $value) : void
    {
        if ($value < 0) {
            throw InvalidVersionException::forNumber($name, $value);
        }
    }

    public static function fromParts(int $major, int $minor = 0, int $patch = 0, PreRelease $preRelease = null, Build $build = null) : Version
    {
        return new static($major, $minor, $patch, $preRelease ?? new NoPreRelease(), $build ?? new NoBuild());
    }

    /**
     * @param string $versionString
     * @return Version
     * @throws InvalidVersionStringException
     */
    public static function fromString(string $versionString) : Version
    {
        if (!preg_match(
            '#^'
            . 'v?'
            . '(?P<core>(?:[0-9]|[1-9][0-9]+)(?:\.(?:[0-9]|[1-9][0-9]+)){2})'
            . '(?:\-(?P<preRelease>[0-9A-Za-z\-\.]+))?'
            . '(?:\+(?P<build>[0-9A-Za-z\-\.]+))?'
            . '$#',
            $versionString,
            $parts
        )) {
            throw InvalidVersionStringException::forVersionString($versionString);
        }

        [$major, $minor, $patch] = explode('.', $parts['core']);
        $preRelease = !empty($parts['preRelease']) ? PreRelease::fromIdentifiersString($parts['preRelease']) : new NoPreRelease();
        $build = !empty($parts['build']) ? Build::fromIdentifiersString($parts['build']) : new NoBuild();

        return static::fromParts((int) $major, (int) $minor, (int) $patch, $preRelease, $build);
    }

    public function getMajor() : int
    {
        return $this->major;
    }

    public function getMinor() : int
    {
        return $this->minor;
    }

    public function getPatch() : int
    {
        return $this->patch;
    }

    public function getPreRelease() : PreRelease
    {
        return $this->preRelease;
    }

    public function getBuild() : Build
    {
        return $this->build;
    }

    public function isPreRelease() : bool
    {
        return !$this->preRelease->isEmpty();
    }

    public function isBuild() : bool
    {
        return !$this->build->isEmpty();
    }

    /**
     * @param Version|string $version
     * @return bool
     */
    public function isEqualTo($version) : bool
    {
        return $this->compareTo($version) === 0;
    }

    /**
     * @param Version|string $version
     * @return bool
     */
    public function isNotEqualTo($version) : bool
    {
        return !$this->isEqualTo($version);
    }

    /**
     * @param Version|string $version
     * @return bool
     */
    public function isGreaterThan($version) : bool
    {
        return $this->compareTo($version) > 0;
    }

    /**
     * @param Version|string $version
     * @return bool
     */
    public function isGreaterOrEqualTo($version) : bool
    {
        return $this->compareTo($version) >= 0;
    }

    /**
     * @param Version|string $version
     * @return bool
     */
    public function isLessThan($version) : bool
    {
        return $this->compareTo($version) < 0;
    }

    /**
     * @param Version|string $version
     * @return bool
     */
    public function isLessOrEqualTo($version) : bool
    {
        return $this->compareTo($version) <= 0;
    }

    /**
     * @param Version|string $version
     * @return int (1 if $this > $version, -1 if $this < $version, 0 if equal)
     */
    public function compareTo($version) : int
    {
        if (is_string($version)) {
            $version = static::fromString($version);
        }

        return $this->getComparator()->compare($this, $version);
    }

    public function incrementMajor() : Version
    {
        return static::fromParts($this->major + 1, 0, 0, new NoPreRelease(), new NoBuild());
    }

    public function incrementMinor() : Version
    {
        return static::fromParts($this->major, $this->minor + 1, 0, new NoPreRelease(), new NoBuild());
    }

    public function incrementPatch() : Version
    {
        return static::fromParts($this->major, $this->minor, $this->patch + 1, new NoPreRelease(), new NoBuild());
    }

    /**
     * @param PreRelease|string $preRelease
     * @return Version
     */
    public function withPreRelease($preRelease) : Version
    {
        if (is_string($preRelease)) {
            $preRelease = PreRelease::fromIdentifiersString($preRelease);
        }

        return static::fromParts($this->major, $this->minor, $this->patch, $preRelease, new NoBuild());
    }

    /**
     * @param Build|string $build
     * @return Version
     */
    public function withBuild($build) : Version
    {
        if (is_string($build)) {
            $build = Build::fromIdentifiersString($build);
        }

        return static::fromParts($this->major, $this->minor, $this->patch, $this->preRelease, $build);
    }

    public function matches(ConstraintInterface $constraint) : bool
    {
        return $constraint->assert($this);
    }

    public function getVersionString() : string
    {
        return
            $this->major
            . '.' . $this->minor
            . '.' . $this->patch
            . ($this->isPreRelease() ? '-' . (string) $this->preRelease : '')
            . ($this->isBuild() ? '+' . (string) $this->build : '')
        ;
    }

    public function __toString() : string
    {
        return $this->getVersionString();
    }

    public function jsonSerialize() : string
    {
        return $this->getVersionString();
    }

    public function toArray() : array
    {
        return [
            'major' => $this->major,
            'minor' => $this->minor,
            'patch' => $this->patch,
            'preRelease' => $this->preRelease->getIdentifiers(),
            'build' => $this->build->getIdentifiers(),
        ];
    }

    protected function getComparator() : ComparatorInterface
    {
        static $comparator = null;

        if (null === $comparator) {
            $comparator = new SemverComparator();
        }

        return $comparator;
    }
}
