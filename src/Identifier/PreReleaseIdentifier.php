<?php

declare(strict_types=1);

namespace Version\Identifier;

use Version\Exception\InvalidIdentifierValueException;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
final class PreReleaseIdentifier extends BaseIdentifier
{
    protected static function validate(string $value) : void
    {
        if (! preg_match('/^[0-9A-Za-z\-]+$/', $value)) {
            throw new InvalidIdentifierValueException(__CLASS__ . ' value must contain only alphanumerics and hyphen');
        }
    }
}
