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
        $firstPreReleaseIdentifiers = array_values($this->getIdentifiers());
        $secondPreReleaseIdentifiers = array_values($preRelease->getIdentifiers());

        $pr1Count = count($firstPreReleaseIdentifiers);
        $pr2Count = count($secondPreReleaseIdentifiers);

        $limit = min($pr1Count, $pr2Count);

        for ($i = 0; $i < $limit; $i++) {
            if ($firstPreReleaseIdentifiers[$i] === $secondPreReleaseIdentifiers[$i]) {
                continue;
            }

            return $this->compareIdentifiers($firstPreReleaseIdentifiers[$i], $secondPreReleaseIdentifiers[$i]);
        }

        return $pr1Count - $pr2Count;
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
