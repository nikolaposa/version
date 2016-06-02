# Change Log
All notable changes to this project will be documented in this file.

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
This release is abandoned, please consider upgrading to 2.0.x.

[Unreleased]: https://github.com/nikolaposa/version/compare/2.0.1...HEAD