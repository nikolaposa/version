<?php

declare(strict_types=1);

namespace Version\Extension;

use Version\Assert\VersionAssert;

class PreRelease extends Extension
{
    protected function validate(array $identifiers): void
    {
        VersionAssert::that($identifiers)
            ->minCount(1, 'Pre-release version must contain at least one identifier')
            ->all()
            ->regex('/^[0-9A-Za-z\-]+$/', 'Pre-release version identifiers can include only alphanumerics and hyphen');
    }
}
