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

    protected int $major;

    protected int $minor;

    protected int $patch;

    protected ?PreRelease $preRelease;

    protected ?Build $build;

    protected string $prefix = '';

    protected static ?Comparator $comparator = null;

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

    public static function from(int $major, int $minor = 0, int $patch = 0, PreRelease $preRelease = null, Build $build = null): Version
    {
        return new static($major, $minor, $patch, $preRelease, $build);
    }

    /**
     * @throws InvalidVersionString
     */
    public static function fromString(string $versionString): Version
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

    public function isEqualTo(Version|string $version): bool
    {
        return $this->compareTo($version) === 0;
    }

    public function isNotEqualTo(Version|string $version): bool
    {
        return !$this->isEqualTo($version);
    }

    public function isGreaterThan(Version|string $version): bool
    {
        return $this->compareTo($version) > 0;
    }

    public function isGreaterOrEqualTo(Version|string $version): bool
    {
        return $this->compareTo($version) >= 0;
    }

    public function isLessThan(Version|string $version): bool
    {
        return $this->compareTo($version) < 0;
    }

    public function isLessOrEqualTo(Version|string $version): bool
    {
        return $this->compareTo($version) <= 0;
    }

    /**
     * @return int (1 if $this > $version, -1 if $this < $version, 0 if equal)
     */
    public function compareTo(Version|string $version): int
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
        return $this->preRelease !== null;
    }

    public function hasBuild(): bool
    {
        return $this->build !== null;
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

    public function withPreRelease(PreRelease|string|null $preRelease): Version
    {
        if (is_string($preRelease)) {
            $preRelease = PreRelease::fromString($preRelease);
        }

        return new static($this->major, $this->minor, $this->patch, $preRelease);
    }

    public function withBuild(Build|string|null $build): Version
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
            . (($this->preRelease !== null) ? '-' . $this->preRelease->toString() : '')
            . (($this->build !== null) ? '+' . $this->build->toString() : '')
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
            'preRelease' => ($this->preRelease !== null) ? $this->preRelease->getIdentifiers() : null,
            'build' => ($this->build !== null) ? $this->build->getIdentifiers() : null,
        ];
    }

    public static function setComparator(?Comparator $comparator): void
    {
        static::$comparator = $comparator;
    }

    protected function getComparator(): Comparator
    {
        if (static::$comparator === null) {
            static::$comparator = new SemverComparator();
        }

        return static::$comparator;
    }
}
