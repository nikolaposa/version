# Version

[![Build](https://github.com/nikolaposa/version/workflows/Build/badge.svg?branch=master)](https://github.com/nikolaposa/version/actions)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nikolaposa/version/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/nikolaposa/version/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/nikolaposa/version/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/nikolaposa/version/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/nikolaposa/version/v/stable)](https://packagist.org/packages/nikolaposa/version)
[![PDS Skeleton](https://img.shields.io/badge/pds-skeleton-blue.svg)](https://github.com/php-pds/skeleton)

Value Object that represents a [SemVer][link-semver]-compliant version number.

## Installation

The preferred method of installation is via [Composer](http://getcomposer.org/). Run the following
command to install the latest version of a package and add it to your project's `composer.json`:

```bash
composer require nikolaposa/version
```

## Usage

### Creating a Version object via named constructor and accessing its values

```php
use Version\Extension\Build;
use Version\Extension\PreRelease;
use Version\Version;

$v = Version::from(2, 0, 0, PreRelease::from('alpha', '1'), Build::from('20191222232137'));

echo $v->getMajor(); //2
echo $v->getMinor(); //0
echo $v->getPatch(); //0
print_r($v->getPreRelease()->getIdentifiers()); //Array([0] => alpha [1] => 1)
echo $v->getPreRelease()->toString(); //alpha.1
print_r($v->getBuild()->getIdentifiers()); //Array([0] => 20191222232137)
echo $v->getBuild()->toString(); //20191222232137
```

### Creating a Version object from a string

```php
$v = Version::fromString('1.10.0');

echo $v->toString(); //1.10.0

```

### Comparing Version objects

```php
$v1 = Version::fromString('1.10.0');
$v2 = Version::fromString('2.3.3');

var_dump($v1->isLessThan($v2)); //bool(true)
var_dump($v1->isGreaterThan($v2)); //bool(false)
var_dump($v1->isEqualTo($v2)); //bool(false)

var_dump($v2->isLessThan($v1)); //bool(false)
var_dump($v2->isGreaterThan($v1)); //bool(true)
```

### Matching Version objects against constraints

```php
$v = Version::fromString('2.2.0');

var_dump($v->matches(OperationConstraint::equalTo(Version::fromString('2.2.0')))); //bool(true)
var_dump($v->matches(OperationConstraint::notEqualTo(Version::fromString('2.2.0')))); //bool(true)
var_dump($v->matches(OperationConstraint::fromString('>=2.0.0 <2.3.0'))); //bool(true)
var_dump($v->matches(OperationConstraint::fromString('>=2.0.0 <2.1.0 || 2.2.0'))); //bool(true)
```

### Version operations

```php
$v = Version::fromString('1.10.0');

$v1101 = $v->incrementPatch();
echo $v1101->toString(); //1.10.1

$v1110 = $v1101->incrementMinor();
echo $v1110->toString(); //1.11.0

$v2 = $v1101->incrementMajor();
echo $v2->toString(); //2.0.0

$v2Alpha = $v2->withPreRelease('alpha');
echo $v2Alpha->toString(); //2.0.0-alpha

$v2Alpha111 = $v2Alpha->withBuild('111');
echo $v2Alpha111->toString(); //2.0.0-alpha+111
```

### Version Collection

```php
$versions = new VersionCollection(
    Version::from(1, 0, 1),
    Version::fromString('1.1.0'),
    Version::fromString('2.3.3')
);

echo count($versions); //3

$versions = $versions->sortedDescending();

//Outputs: 2.3.3, 1.1.0, 1.0.1
foreach ($versions as $version) {
    echo $version->toString();
}

$minorReleases = $versions->minorReleases();
echo $minorReleases->first(); //1.1.0
```

## Credits

- [Nikola Poša][link-author]
- [All Contributors][link-contributors]

## License

Released under MIT License - see the [License File](LICENSE) for details.


[link-semver]: http://semver.org/
[link-author]: https://github.com/nikolaposa
[link-contributors]: ../../contributors
