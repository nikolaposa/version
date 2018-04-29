<?php

declare(strict_types=1);

namespace Version\Constraint;

use Version\Version;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
interface ConstraintInterface
{
    public function assert(Version $version) : bool;
}
