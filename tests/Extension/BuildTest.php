<?php

declare(strict_types=1);

namespace Version\Tests\Extension;

use Version\Extension\Extension;
use Version\Extension\Build;

class BuildTest extends ExtensionTest
{
    protected function createExtension($identifiers): Extension
    {
        if (is_string($identifiers)) {
            return Build::fromString($identifiers);
        }

        return Build::from(...$identifiers);
    }
}
