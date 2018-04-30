<?php

declare(strict_types=1);

namespace Version;

use JsonSerializable;
use Version\Extension\Build;
use Version\Extension\NoBuild;
use Version\Extension\NoPreRelease;
use Version\Exception\InvalidVersionElementException;
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
        $this->major = $major;
        $this->minor = $minor;
        $this->patch = $patch;
        $this->preRelease = $preRelease;
        $this->build = $build;
    }

    public static function fromParts(int $major, int $minor, int $patch, PreRelease $preRelease, Build $build) : Version
    {
        static::validatePart('major', $major);
        static::validatePart('minor', $minor);
        static::validatePart('patch', $patch);

        return new static($major, $minor, $patch, $preRelease, $build);
    }

    public static function fromMajor(int $major) : Version
    {
        return static::fromParts($major, 0, 0, new NoPreRelease(), new NoBuild());
    }

    public static function fromMinor(int $major, int $minor) : Version
    {
        return static::fromParts($major, $minor, 0, new NoPreRelease(), new NoBuild());
    }

    public static function fromPatch(int $major, int $minor, int $patch) : Version
    {
        return static::fromParts($major, $minor, $patch, new NoPreRelease(), new NoBuild());
    }

    public static function fromPreRelease(int $major, int $minor, int $patch, PreRelease $preRelease) : Version
    {
        return static::fromParts($major, $minor, $patch, $preRelease, new NoBuild());
    }

    public static function fromBuild(int $major, int $minor, int $patch, Build $build) : Version
    {
        return static::fromParts($major, $minor, $patch, new NoPreRelease(), $build);
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
        $major = (int) $major;
        $minor = (int) $minor;
        $patch = (int) $patch;

        $preRelease = !empty($parts['preRelease']) ? PreRelease::fromIdentifiersString($parts['preRelease']) : new NoPreRelease();

        $build = !empty($parts['build']) ? Build::fromIdentifiersString($parts['build']) : new NoBuild();

        return static::fromParts($major, $minor, $patch, $preRelease, $build);
    }

    protected static function validatePart(string $part, int $value) : void
    {
        if ($value < 0) {
            throw InvalidVersionElementException::forElement($part);
        }
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
     * @param self|string $version
     * @return bool
     */
    public function isEqualTo($version) : bool
    {
        return $this->compareTo($version) === 0;
    }

    /**
     * @param self|string $version
     * @return bool
     */
    public function isNotEqualTo($version) : bool
    {
        return !$this->isEqualTo($version);
    }

    /**
     * @param self|string $version
     * @return bool
     */
    public function isGreaterThan($version) : bool
    {
        return $this->compareTo($version) > 0;
    }

    /**
     * @param self|string $version
     * @return bool
     */
    public function isGreaterOrEqualTo($version) : bool
    {
        return $this->compareTo($version) >= 0;
    }

    /**
     * @param self|string $version
     * @return bool
     */
    public function isLessThan($version) : bool
    {
        return $this->compareTo($version) < 0;
    }

    /**
     * @param self|string $version
     * @return bool
     */
    public function isLessOrEqualTo($version) : bool
    {
        return $this->compareTo($version) <= 0;
    }

    /**
     * @param self|string $version
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

    public function withPreRelease($preRelease) : Version
    {
        if (is_string($preRelease)) {
            $preRelease = PreRelease::fromIdentifiersString($preRelease);
        }

        return static::fromParts($this->major, $this->minor, $this->patch, $preRelease, new NoBuild());
    }

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
