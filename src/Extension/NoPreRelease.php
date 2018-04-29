<?php

declare(strict_types=1);

namespace Version\Extension;

class NoPreRelease extends PreRelease
{
    public function __construct()
    {
        parent::__construct(...[]);
    }
}
