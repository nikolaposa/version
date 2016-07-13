<?php

/**
 * This file is part of the Version package.
 *
 * Copyright (c) Nikola Posa <posa.nikola@gmail.com>
 *
 * For full copyright and license information, please refer to the LICENSE file,
 * located at the package root folder.
 */

namespace Version\Constraint;

use Version\Version;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
interface ConstraintInterface
{
    /**
     * @param Version $version
     * @return bool
     */
    public function assert(Version $version);
}
