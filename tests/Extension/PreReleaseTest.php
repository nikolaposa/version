<?php

declare(strict_types=1);

namespace Version\Tests\Extension;

use Version\Extension\Extension;
use Version\Extension\PreRelease;

class PreReleaseTest extends ExtensionTest
{
    protected function createExtension($identifiers): Extension
    {
        if (is_string($identifiers)) {
            return PreRelease::fromString($identifiers);
        }

        return PreRelease::from(...$identifiers);
    }
}
