<?php

declare(strict_types=1);

namespace Version\Extension;

class NoBuild extends Build
{
    public function __construct()
    {
        parent::__construct(...[]);
    }
}
