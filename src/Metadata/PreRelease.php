<?php

declare(strict_types=1);

namespace Version\Metadata;

use Version\Identifier\Identifier;
use Version\Identifier\PreReleaseIdentifier;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
final class PreRelease extends BaseIdentifyingMetadata
{
    protected static function createAssociatedIdentifier(string $value) : Identifier
    {
        return PreReleaseIdentifier::create($value);
    }

    public function compareTo(PreRelease $preRelease) : int
    {
        $pr1Ids = array_values($this->getIdentifiers());
        $pr2Ids = array_values($preRelease->getIdentifiers());

        $pr1Count = count($pr1Ids);
        $pr2Count = count($pr2Ids);

        $limit = min($pr1Count, $pr2Count);

        for ($i = 0; $i < $limit; $i++) {
            $pr1IdVal = $pr1Ids[$i]->getValue();
            $pr2IdVal = $pr2Ids[$i]->getValue();

            if ($pr1IdVal === $pr2IdVal) {
                continue;
            }

            return $this->comparePreReleaseIdentifierValues($pr1IdVal, $pr2IdVal);
        }

        return $pr1Count - $pr2Count;
    }

    private function comparePreReleaseIdentifierValues($pr1IdVal, $pr2IdVal) : int
    {
        $pr1IsAlpha = ctype_alpha($pr1IdVal);
        $pr2IsAlpha = ctype_alpha($pr2IdVal);

        if ($pr1IsAlpha xor $pr2IsAlpha) {
            return $pr1IsAlpha ? 1 : -1;
        }

        if (ctype_digit($pr1IdVal) && ctype_digit($pr2IdVal)) {
            return (int) $pr1IdVal - (int) $pr2IdVal;
        }

        return strcmp($pr1IdVal, $pr2IdVal);
    }
}
