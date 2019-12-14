<?php

declare(strict_types=1);

namespace Version\Extension;

use Version\Assert\VersionAssert;

class Build extends BaseExtension
{
    public static function empty(): Build
    {
        static $noBuild = null;

        if (null === $noBuild) {
            $noBuild = new self(...[]);
        }

        return $noBuild;
    }

    protected function validate(string $identifier): void
    {
        VersionAssert::that($identifier)->regex(
            '/^[0-9A-Za-z\-]+$/',
            'Build metadata is not valid; identifiers must include only alphanumerics and hyphen'
        );
    }
}
