<?php
/**
 * This file is part of the Version package.
 *
 * Copyright (c) Nikola Posa <posa.nikola@gmail.com>
 *
 * For full copyright and license information, please refer to the LICENSE file,
 * located at the package root folder.
 */

namespace Version\Identifier;

use Version\Exception\InvalidIdentifierValueException;

/**
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
final class PreRelease extends BaseIdentifier
{
    /**
     * @param string $value
     * @throws InvalidIdentifierValueException
     */
    protected function validate($value)
    {
        parent::validate($value);

        if (!preg_match('/^[0-9A-Za-z\-]+$/', $value)) {
            throw new InvalidIdentifierValueException(__CLASS__ . ' value must contain only alphanumerics and hyphen');
        }
    }
}
