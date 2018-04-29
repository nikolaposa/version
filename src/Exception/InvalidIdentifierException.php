<?php

declare(strict_types=1);

namespace Version\Exception;

use DomainException;
use Version\Extension\BaseExtension;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class InvalidIdentifierException extends DomainException implements ExceptionInterface
{
    public static function forExtensionIdentifier(BaseExtension $extension, string $identifier) : self
    {
        return new self(sprintf(
            '%s identifier: %s is not valid; it must comprise only ASCII alphanumerics and hyphen',
            get_class($extension),
            $identifier
        ));
    }
}
