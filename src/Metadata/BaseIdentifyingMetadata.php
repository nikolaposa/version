<?php
/**
 * This file is part of the Version package.
 *
 * Copyright (c) Nikola Posa <posa.nikola@gmail.com>
 *
 * For full copyright and license information, please refer to the LICENSE file,
 * located at the package root folder.
 */

namespace Version\Metadata;

use Version\Identifier\Identifier;
use Version\Exception\InvalidArgumentException;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
abstract class BaseIdentifyingMetadata
{
    /**
     * @var Identifier[]
     */
    protected $identifiers;

    /**
     * @var string
     */
    protected static $identifierClass = null;

    /**
     * @param array|string $ids Identifiers
     */
    public function __construct($ids)
    {
        $identifiers = [];

        $identifierClass = static::$identifierClass;

        if (is_string($ids)) {
            if (strpos($ids, '.') !== false) {
                $parts = explode('.', $ids);

                foreach ($parts as $val) {
                    $identifiers[]= new $identifierClass($val);
                }
            } else {
                $identifiers = [new $identifierClass($ids)];
            }
        } elseif (is_array($ids)) {
            foreach ($ids as $id) {
                if (!$id instanceof $identifierClass) {
                    $identifiers[]= new $identifierClass($id);
                }
            }
        } else {
            throw new InvalidArgumentException('Identifiers parameter should be either string or array');
        }

        $this->identifiers = $identifiers;
    }

    /**
     * @return array
     */
    public function getIdentifiers()
    {
        return $this->identifiers;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode('.', $this->getIdentifiers());
    }
}
