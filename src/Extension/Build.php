<?php

declare(strict_types=1);

namespace Version\Extension;

use Version\Assert\VersionAssert;

class Build extends Extension
{
    protected function validate(array $identifiers): void
    {
        VersionAssert::that($identifiers)
            ->minCount(1, 'Build metadata must contain at least one identifier')
            ->all()
            ->regex('/^[0-9A-Za-z\-]+$/', 'Build metadata identifiers can include only alphanumerics and hyphen');
    }
}
