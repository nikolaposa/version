<?php

declare(strict_types=1);

namespace Version\Exception;

use Assert\InvalidArgumentException;

class InvalidVersion extends InvalidArgumentException implements VersionException
{
}
