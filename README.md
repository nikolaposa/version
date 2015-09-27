# Version

[![Latest Stable Version](https://poser.pugx.org/nikolaposa/version/v/stable)](https://packagist.org/packages/nikolaposa/version)
[![Build Status](https://travis-ci.org/nikolaposa/version.svg?branch=master)](https://travis-ci.org/nikolaposa/version)

Value Object representing a version number that is in compliance with the [Semantic Versioning specification][semver].

## Installation

Install the library using [composer](http://getcomposer.org/). Add the following to your `composer.json`:

```json
{
    "require": {
        "nikolaposa/version": "1.*"
    }
}
```

Tell composer to download Version by running `install` command:

```bash
$ php composer.phar install
```

## Usage

### Creating a Version object and accessing its values

```php
use Version\Version;

$v = new Version(2, 0, 0, 'alpha');

echo $v->getMajor(); //2
echo $v->getMinor(); //0
echo $v->getPatch(); //0
var_dump($v->getPreRelease()->getIdentifiers()); //array(1) { [0]=> string(1) "alpha" }
echo $v->getPreRelease(); //alpha

```

### Creating a Version object from a string

```php
use Version\Version;

$v = Version::fromString('1.10.0');

echo $v->getVersionString(); //1.10.0

```

### Comparing Version objects

```php
use Version\Version;

$v1 = Version::fromString('1.10.0');
$v2 = Version::fromString('2.3.3');

var_dump($v1->isLessThan($v2)); //bool(true)
var_dump($v1->isGreaterThan($v2)); //bool(false)
var_dump($v1->isEqualTo($v2)); //bool(false)

var_dump($v2->isLessThan($v1)); //bool(false)
var_dump($v2->isGreaterThan($v1)); //bool(true)

```

### Incrementing Version object

```php
use Version\Version;

$v = Version::fromString('1.10.0');

$v1101 = $v->incrementPatch(null, '20150919');
echo $v1101; //1.10.1+20150919

$v1110 = $v1101->incrementMinor();
echo $v1110; //1.11.0

$v2 = $v1101->incrementMajor('alpha');
echo $v2; //2.0.0-alpha

```

### Version-aware objects

```php
use Version\Version;
use Version\VersionAwareInterface;
use Version\VersionAwareTrait;

class Package implements VersionAwareInterface
{
    use VersionAwareTrait;

    private $name;

    private $description;

    public function __construct($name, $description = '')
    {
        $this->name = $name;
        $this->description = $description;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }
}

$package = new Package('Test');
$package->setVersion(Version::fromString('2.3.3'));

$package->setVersion($package->getVersion()->incrementMinor());

```

[semver]: http://semver.org/
