<?php

declare(strict_types=1);

namespace Version\Comparison\Constraint;

use Version\Version;

interface Constraint
{
    public function assert(Version $version): bool;
}
