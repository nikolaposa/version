# Change Log
All notable changes to this project will be documented in this file.

## 4.1.0 – 2020-12-12

- [35: Allow PHP 8 installations](https://github.com/nikolaposa/version/pull/35)
- PHPStan integration
- Migration to GitHub Actions thanks to [Andreas Möller](https://github.com/localheinz)
  - [PHPStan](https://github.com/nikolaposa/version/pull/28)
  - [PHP CS-Fixer](https://github.com/nikolaposa/version/pull/29)
  - [PHPUnit](https://github.com/nikolaposa/version/pull/30)

## 4.0.0 - 2019-12-29

### Changed
- PHPUnit 8 is now the minimum required version
- Rename `VersionsCollection` to `VersionCollection`
- `Version->getPreRelease()` now explicitly returns nullable type
- `Version->getBuild()` now explicitly returns nullable type
- Rename `Version::fromParts()` to `Version::from()`
- Rename `Version->getVersionString()` to `Version->toString()`
- Use `beberlei/assert` library for input validation
- Rename `Version\Comparator` namespace to `Version\Comparison`
- Move `Version\Constraint` namespace to `Version\Comparison\Constraint`
- Rename `ComparatorInterface` to `Comparator`
- Rename `ComparisonConstraint` to `OpeationConstraint`
- Rename `ComparisonConstraintParser` to `OpeationConstraintParser`
- Rename component-level `Version\Exception\ExceptionInterface` to `Version\Exception\VersionException`
- Move comparision-related exceptions into `Version\Comparision\Exception` namespace
- Remove `-Exception` suffix from all concrete exception names
- Rename `BaseExtension` to `Extension`
- Rename `PreRelease::fromIdentifiers()` to `PreRelease::from()`
- Rename `Build::fromIdentifiers()` to `Build::from()`
- Rename `PreRelease::fromIdentifiersString()` to `PreRelease::fromString()`
- Rename `Build::fromIdentifiersString()` to `Build::fromString()`

### Removed
- `Version\VersionAwareInterface`
- `Version\VersionAwareTrait`
- `Version\Extension\NoPreRelease`
- `Version\Extension\NoBuild`
- `Version->isBuild()`
- `VersionCollection->sort()`
- `PreRelease->isEmpty()`
- `PreRelease->__toString()`
- `Build->isEmpty()`
- `Build->__toString()`

### Added
- `OperationConstraint::equalsTo()` named constructor
- `OperationConstraint::notEqualTo()` named constructor
- `OperationConstraint::greaterThan()` named constructor
- `OperationConstraint::greaterOrEqualTo()` named constructor
- `OperationConstraint::lessThan()` named constructor
- `OperationConstraint::lessOrEqualTo()` named constructor
- Prefix supplied in `Version::fromString()` is captured and included in `toString()` result


## 3.2.0 - 2019-08-11
### Added
- [23: Allow setting custom comparison strategy](https://github.com/nikolaposa/version/pull/23)

### Fixed
- [20: Fix comparison when identifier of pre release contains both letters and numbers](https://github.com/nikolaposa/version/pull/20)
- [24: Fix docblocks for withPreRelease() and withBuild() methods](https://github.com/nikolaposa/version/pull/24)

## 3.1.0 - 2018-06-14
### Added
- Support for parsing Composer version strings
- Add `VersionsCollection::isEmpty()` method
- Add `VersionsCollection::toArray()` method
- Add immutable `VersionsCollection::sortedAscending()` and `VersionsCollection::sortedDescending()` methods; mark `VersionsCollection::sort()` as deprecated
- Add `VersionsCollection::first()` method
- Add `VersionsCollection::last()` method
- Add `VersionsCollection::majorReleases()` method
- Add `VersionsCollection::minorReleases()` method
- Add `VersionsCollection::patchReleases()` method

## 3.0.2 - 2018-05-28
### Fixed
- [Lower PHP version requirement](https://github.com/nikolaposa/version/issues/19)

## 3.0.1 - 2018-05-05
### Fixed
- `VersionsCollection::matching()` fails if all the versions do not satisfy constraint; allow VersionsCollection to be empty

## 3.0.0 - 2018-05-02
### Added
- Strict typing wherever possible
- [15: Relaxed version parsing](https://github.com/nikolaposa/version/pull/15)

### Changed
- [13: PHP 7.2 as minimum requirement](https://github.com/nikolaposa/version/pull/13)
- Renamed `Version::fromElements()` to `Version::fromParts()`
- `Version::fromParts()` requires `PreRelease` and `Build` instances
- Renamed `Version::withMajorIncremented()` to `Version::incrementMajor()`
- Renamed `Version::withMinorIncremented()` to `Version::incrementMinor()`
- Renamed `Version::withPatchIncremented()` to `Version::incrementPatch()`
- [14: Simplified modeling of extension parts - pre-release and build](https://github.com/nikolaposa/version/pull/14)
- [16: Simplified Constraint modeling](https://github.com/nikolaposa/version/pull/16)

### Removed
- Setter method from the `VersionAwareInterface`
- `Version::from(Major|Minor|Path|PreRelease|Build)` named constructors in favor of having a single `Version::fromParts()` named constructor with optional parameters
- `VersionCollection::fromArray()`; constructor with variadic `Version` arguments should be used instead

## 2.2.2 - 2017-01-01
### Fixed
- [11: Include version string in the exception message](https://github.com/nikolaposa/version/pull/11)

## 2.2.1 - 2017-01-01
### Fixed
- Coding standard fixes
- Using friendsofphp/php-cs-fixer instead of abandoned fabpot/php-cs-fixer.

## 2.2.0 - 2016-07-14
### Added
- [8: Mechanism for matching versions against constraints](https://github.com/nikolaposa/version/pull/8).
- [9: Support for having custom version comparison strategies](https://github.com/nikolaposa/version/pull/9).
- `Version::isNotEqualTo()` method.

## 2.1.0 - 2016-06-11
### Added
- [5: Version is now JsonSerializable](https://github.com/nikolaposa/version/pull/5).
- [6: Capability for converting Version into array](https://github.com/nikolaposa/version/pull/6).
- Static factory method (`fromArray`) for creating VersionsCollection.

## 2.0.1 - 2016-06-02
### Added
- Hooked [PHP Coding Standards Fixer](http://cs.sensiolabs.org/) into the Travis CI.

### Fixed
- Fixed examples in README.
- Creating empty Metadata objects in favor of passing around `null` values.
- [3: Fix wrong output in readme](https://github.com/nikolaposa/version/pull/3)
- [4: Remove useless cloning](https://github.com/nikolaposa/version/pull/4)

## 2.0.0 - 2016-06-02
### Added
- Static factory methods, for example `Version::fromMajor()`, `Version::fromPreRelease()`.
- Methods for modifying pre-release and build information of a version.

### Backwards-incompatible changes
- Static factory methods (named constructors) are used to instantiate objects instead of constructors.
- `increment*` methods in `Version\Version` class were renamed to `with*Incremented`.
- `Version\Identifier\PreRelease` renamed to `Version\Identifier\PreReleaseIdentifier`.
- `Version\Identifier\Build` renamed to `Version\Identifier\BuildIdentifier`.

## 1.2.x
This release is abandoned, please consider upgrading to 2.x.


[Unreleased]: https://github.com/nikolaposa/version/compare/4.1.0...HEAD
