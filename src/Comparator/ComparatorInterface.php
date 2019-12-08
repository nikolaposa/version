<?php

declare(strict_types=1);

namespace Version\Comparator;

use Version\Version;

interface ComparatorInterface
{
    public function compare(Version $version1, Version $version2): int;
}
