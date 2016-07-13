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

use Version\Identifier\PreReleaseIdentifier;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
final class PreRelease extends BaseIdentifyingMetadata
{
    protected static function createAssociatedIdentifier($value)
    {
        return PreReleaseIdentifier::create($value);
    }

    /**
     * @param self $preRelease
     * @return int (> 0 if $this > $preRelease, < 0 if $this < $preRelease, 0 if equal)
     */
    public function compareTo(PreRelease $preRelease)
    {
        $pr1Ids = array_values($this->getIdentifiers());
        $pr2Ids = array_values($preRelease->getIdentifiers());

        $pr1Count = count($pr1Ids);
        $pr2Count = count($pr2Ids);

        $limit = min($pr1Count, $pr2Count);

        for ($i = 0; $i < $limit; $i++) {
            $pr1IdVal = $pr1Ids[$i]->getValue();
            $pr2IdVal = $pr2Ids[$i]->getValue();

            if ($pr1IdVal == $pr2IdVal) {
                continue;
            }

            return $this->comparePreReleaseIdentifierValues($pr1IdVal, $pr2IdVal);
        }

        return $pr1Count - $pr2Count;
    }

    private function comparePreReleaseIdentifierValues($pr1IdVal, $pr2IdVal)
    {
        $pr1IsAlpha = ctype_alpha($pr1IdVal);
        $pr2IsAlpha = ctype_alpha($pr2IdVal);

        if ($pr1IsAlpha && !$pr2IsAlpha) {
            return 1;
        }

        if ($pr2IsAlpha && !$pr1IsAlpha) {
            return -1;
        }

        if (ctype_digit($pr1IdVal) && ctype_digit($pr2IdVal)) {
            return (int) $pr1IdVal - (int) $pr2IdVal;
        }

        return strcmp($pr1IdVal, $pr2IdVal);
    }
}
