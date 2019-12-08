<?php

declare(strict_types=1);

namespace Version\Comparison;

use Version\Version;

interface Comparator
{
    public function compare(Version $version1, Version $version2): int;
}
