<?php

declare(strict_types=1);

namespace Version\Extension;

use Version\Assert\VersionAssert;

class PreRelease extends Extension
{
    protected function validate(string $identifier): void
    {
        VersionAssert::that($identifier)->regex(
            '/^[0-9A-Za-z\-]+$/',
            'Pre-release version is not valid; identifiers must include only alphanumerics and hyphen'
        );
    }

    public function compareTo(PreRelease $preRelease): int
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

    private function compareIdentifiers($identifier1, $identifier2): int
    {
        $identifier1IsNumber = ctype_digit($identifier1);
        $identifier2IsNumber = ctype_digit($identifier2);

        if ($identifier1IsNumber xor $identifier2IsNumber) {
            return $identifier1IsNumber ? -1 : 1;
        }

        if ($identifier1IsNumber && $identifier2IsNumber) {
            return (int) $identifier1 - (int) $identifier2;
        }

        return strcmp($identifier1, $identifier2);
    }
}
