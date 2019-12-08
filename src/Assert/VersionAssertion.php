<?php

declare(strict_types=1);

namespace Version\Assert;

use Assert\Assertion;
use Version\Exception\InvalidVersion;

class VersionAssertion extends Assertion
{
    protected static $exceptionClass = InvalidVersion::class;
}
