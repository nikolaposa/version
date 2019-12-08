<?php

declare(strict_types=1);

namespace Version\Exception;

use DomainException;
use ReflectionClass;
use Version\Extension\BaseExtension;

class InvalidExtensionIdentifier extends DomainException implements VersionException
{
    public static function forExtensionIdentifier(BaseExtension $extension, string $identifier): self
    {
        $extensionName = (new ReflectionClass($extension))->getShortName();

        return new self(sprintf(
            "%s identifier: '%s' is not valid; it must comprise only ASCII alphanumerics and hyphen",
            $extensionName,
            $identifier
        ));
    }
}
