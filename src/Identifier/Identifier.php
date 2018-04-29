<?php

declare(strict_types=1);

namespace Version\Identifier;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
interface Identifier
{
    public function getValue() : string;
}
