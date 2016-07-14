<?php

/**
 * This file is part of the Version package.
 *
 * Copyright (c) Nikola Posa <posa.nikola@gmail.com>
 *
 * For full copyright and license information, please refer to the LICENSE file,
 * located at the package root folder.
 */

namespace Version\Comparator;

use Version\Version;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
interface ComparatorInterface
{
    /**
     * @param Version $version1
     * @param Version $version2
     * @return int (1 if $this > $version, -1 if $this < $version, 0 if equal)
     */
    public function compare(Version $version1, Version $version2);
}
