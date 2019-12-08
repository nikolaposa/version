<?php

declare(strict_types=1);

namespace Version\Constraint;

use Version\Version;

interface ConstraintInterface
{
    public function assert(Version $version): bool;
}
