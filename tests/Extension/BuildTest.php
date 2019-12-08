<?php

declare(strict_types=1);

namespace Version\Tests\Extension;

use Version\Extension\BaseExtension;
use Version\Extension\Build;

class BuildTest extends BaseExtensionTest
{
    protected function createExtension($identifiers): BaseExtension
    {
        if (is_string($identifiers)) {
            return Build::fromIdentifiersString($identifiers);
        }

        return Build::fromIdentifiers(...$identifiers);
    }
}
