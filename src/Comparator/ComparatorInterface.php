<?php

declare(strict_types=1);

namespace Version\Comparator;

use Version\Version;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
interface ComparatorInterface
{
    public function compare(Version $version1, Version $version2) : int;
}
