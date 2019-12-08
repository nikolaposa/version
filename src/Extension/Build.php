<?php

declare(strict_types=1);

namespace Version\Extension;

use Version\Exception\InvalidExtensionIdentifier;

class Build extends BaseExtension
{
    protected function validate(string $identifier): void
    {
        if (! preg_match('/^[0-9A-Za-z\-]+$/', $identifier)) {
            throw InvalidExtensionIdentifier::forExtensionIdentifier($this, $identifier);
        }
    }
}
