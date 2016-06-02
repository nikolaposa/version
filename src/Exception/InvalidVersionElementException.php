<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Version\Exception;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class InvalidVersionElementException extends InvalidArgumentException
{
    public static function forElement($element)
    {
        return new self(sprintf(
            '%s version must be non-negative integer',
            ucfirst($element)
        ));
    }
}
