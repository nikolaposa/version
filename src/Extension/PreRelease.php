<?php

declare(strict_types=1);

namespace Version\Extension;

use Version\Exception\InvalidIdentifierException;

class PreRelease extends BaseExtension
{
    protected function validate(string $identifier) : void
    {
        if (! preg_match('/^[0-9A-Za-z\-]+$/', $identifier)) {
            throw InvalidIdentifierException::forExtensionIdentifier($this, $identifier);
        }
    }

    public function compareTo(PreRelease $preRelease) : int
    {
        $preRelease1Ids = array_values($this->getIdentifiers());
        $preRelease2Ids = array_values($preRelease->getIdentifiers());

        $preRelease1IdsCount = count($preRelease1Ids);
        $preRelease2IdsCount = count($preRelease2Ids);

        $limit = min($preRelease1IdsCount, $preRelease2IdsCount);

        for ($i = 0; $i < $limit; $i++) {
            if ($preRelease1Ids[$i] === $preRelease2Ids[$i]) {
                continue;
            }

            return $this->compareIdentifiers($preRelease1Ids[$i], $preRelease2Ids[$i]);
        }

        return $preRelease1IdsCount - $preRelease2IdsCount;
    }

    private function compareIdentifiers($identifier1, $identifier2) : int
    {
        $pr1IsAlpha = ctype_alpha($identifier1);
        $pr2IsAlpha = ctype_alpha($identifier2);

        if ($pr1IsAlpha xor $pr2IsAlpha) {
            return $pr1IsAlpha ? 1 : -1;
        }

        if (ctype_digit($identifier1) && ctype_digit($identifier2)) {
            return (int) $identifier1 - (int) $identifier2;
        }

        return strcmp($identifier1, $identifier2);
    }
}
