<?php

declare(strict_types=1);

namespace Version;

use JsonSerializable;
use Version\Assert\VersionAssert;
use Version\Extension\Build;
use Version\Exception\InvalidVersionString;
use Version\Comparison\Comparator;
use Version\Comparison\SemverComparator;
use Version\Comparison\Constraint\Constraint;
use Version\Extension\PreRelease;

class Version implements JsonSerializable
{
    public const REGEX = '#^(?P<prefix>v|release\-)?(?P<major>0|[1-9]\d*)\.(?P<minor>0|[1-9]\d*)\.(?P<patch>0|[1-9]\d*)(?:\-(?P<preRelease>(?:0|[1-9]\d*|\d*[a-zA-Z\-][0-9a-zA-Z\-]*)(?:\.(?:0|[1-9]\d*|\d*[a-zA-Z\-][0-9a-zA-Z\-]*))*))?(?:\+(?P<build>[0-9a-zA-Z\-]+(?:\.[0-9a-zA-Z\-]+)*))?$#';

    /** @var int */
    protected $major;

    /** @var int */
    protected $minor;

    /** @var int */
    protected $patch;

    /** @var PreRelease|null */
    protected $preRelease;

    /** @var Build|null */
    protected $build;

    /** @var string */
    protected $prefix;

    /** @var Comparator|null */
    protected static $comparator;

    final protected function __construct(int $major, int $minor, int $patch, PreRelease $preRelease = null, Build $build = null)
    {
        VersionAssert::that($major)->greaterOrEqualThan(0, 'Major version must be positive integer');
        VersionAssert::that($minor)->greaterOrEqualThan(0, 'Minor version must be positive integer');
        VersionAssert::that($patch)->greaterOrEqualThan(0, 'Patch version must be positive integer');

        $this->major = $major;
        $this->minor = $minor;
        $this->patch = $patch;
        $this->preRelease = $preRelease;
        $this->build = $build;
    }

    public static function from(int $major, int $minor = 0, int $patch = 0, PreRelease $preRelease = null, Build $build = null)
    {
        return new static($major, $minor, $patch, $preRelease, $build);
    }

    /**
     * @throws InvalidVersionString
     */
    public static function fromString(string $versionString)
    {
        if (!preg_match(self::REGEX, $versionString, $parts)) {
            throw InvalidVersionString::notParsable($versionString);
        }

        $version = new static(
            (int) $parts['major'],
            (int) $parts['minor'],
            (int) $parts['patch'],
            (isset($parts['preRelease']) && '' !== $parts['preRelease']) ? PreRelease::fromString($parts['preRelease']) : null,
            (isset($parts['build']) && '' !== $parts['build']) ? Build::fromString($parts['build']) : null
        );
        $version->prefix = $parts['prefix'] ?? '';

        return $version;
    }

    public function getMajor(): int
    {
        return $this->major;
    }

    public function getMinor(): int
    {
        return $this->minor;
    }

    public function getPatch(): int
    {
        return $this->patch;
    }

    public function getPreRelease(): ?PreRelease
    {
        return $this->preRelease;
    }

    public function getBuild(): ?Build
    {
        return $this->build;
    }

    /**
     * @param Version|string $version
     * @return bool
     */
    public function isEqualTo($version): bool
    {
        return $this->compareTo($version) === 0;
    }

    /**
     * @param Version|string $version
     * @return bool
     */
    public function isNotEqualTo($version): bool
    {
        return !$this->isEqualTo($version);
    }

    /**
     * @param Version|string $version
     * @return bool
     */
    public function isGreaterThan($version): bool
    {
        return $this->compareTo($version) > 0;
    }

    /**
     * @param Version|string $version
     * @return bool
     */
    public function isGreaterOrEqualTo($version): bool
    {
        return $this->compareTo($version) >= 0;
    }

    /**
     * @param Version|string $version
     * @return bool
     */
    public function isLessThan($version): bool
    {
        return $this->compareTo($version) < 0;
    }

    /**
     * @param Version|string $version
     * @return bool
     */
    public function isLessOrEqualTo($version): bool
    {
        return $this->compareTo($version) <= 0;
    }

    /**
     * @param Version|string $version
     * @return int (1 if $this > $version, -1 if $this < $version, 0 if equal)
     */
    public function compareTo($version): int
    {
        if (is_string($version)) {
            $version = static::fromString($version);
        }

        return $this->getComparator()->compare($this, $version);
    }

    public function isMajorRelease(): bool
    {
        return $this->major > 0 && $this->minor === 0 && $this->patch === 0;
    }

    public function isMinorRelease(): bool
    {
        return $this->minor > 0 && $this->patch === 0;
    }

    public function isPatchRelease(): bool
    {
        return $this->patch > 0;
    }

    public function isPreRelease(): bool
    {
        return null !== $this->preRelease;
    }

    public function hasBuild(): bool
    {
        return null !== $this->build;
    }

    public function incrementMajor(): Version
    {
        return new static($this->major + 1, 0, 0);
    }

    public function incrementMinor(): Version
    {
        return new static($this->major, $this->minor + 1, 0);
    }

    public function incrementPatch(): Version
    {
        return new static($this->major, $this->minor, $this->patch + 1);
    }

    /**
     * @param PreRelease|string|null $preRelease
     * @return Version
     */
    public function withPreRelease($preRelease): Version
    {
        if (is_string($preRelease)) {
            $preRelease = PreRelease::fromString($preRelease);
        }

        return new static($this->major, $this->minor, $this->patch, $preRelease);
    }

    /**
     * @param Build|string|null $build
     * @return Version
     */
    public function withBuild($build): Version
    {
        if (is_string($build)) {
            $build = Build::fromString($build);
        }

        return new static($this->major, $this->minor, $this->patch, $this->preRelease, $build);
    }

    public function matches(Constraint $constraint): bool
    {
        return $constraint->assert($this);
    }

    public function toString(): string
    {
        return
            $this->prefix
            . $this->major
            . '.' . $this->minor
            . '.' . $this->patch
            . ((null !== $this->preRelease) ? '-' . $this->preRelease->toString() : '')
            . ((null !== $this->build) ? '+' . $this->build->toString() : '')
        ;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    public function toArray(): array
    {
        return [
            'major' => $this->major,
            'minor' => $this->minor,
            'patch' => $this->patch,
            'preRelease' => (null !== $this->preRelease) ? $this->preRelease->getIdentifiers() : null,
            'build' => (null !== $this->build) ? $this->build->getIdentifiers() : null,
        ];
    }

    public static function setComparator(?Comparator $comparator): void
    {
        static::$comparator = $comparator;
    }

    protected function getComparator(): Comparator
    {
        if (null === static::$comparator) {
            static::$comparator = new SemverComparator();
        }

        return static::$comparator;
    }
}
