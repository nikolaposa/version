<?php

declare(strict_types=1);

namespace Version\Assert;

use Assert\Assert;

class VersionAssert extends Assert
{
    protected static $assertionClass = VersionAssertion::class;
}
