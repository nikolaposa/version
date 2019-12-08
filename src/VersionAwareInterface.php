<?php

declare(strict_types=1);

namespace Version;

interface VersionAwareInterface
{
    public function getVersion(): Version;
}
