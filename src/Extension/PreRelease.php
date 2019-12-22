<?php

declare(strict_types=1);

namespace Version\Extension;

use Version\Assert\VersionAssert;

class PreRelease extends Extension
{
    protected function validate(array $identifiers): void
    {
        VersionAssert::that($identifiers)
            ->minCount(1, 'Pre-release version must contain at least one identifier')
            ->all()
            ->regex('/^[0-9A-Za-z\-]+$/', 'Pre-release version identifiers can include only alphanumerics and hyphen');
    }

    public function compareTo(PreRelease $preRelease): int
    {
        $preRelease1Ids = $this->getIdentifiers();
        $preRelease2Ids = $preRelease->getIdentifiers();

        $preRelease1IdsCount = count($preRelease1Ids);
        $preRelease2IdsCount = count($preRelease2Ids);

        $limit = min($preRelease1IdsCount, $preRelease2IdsCount);

        for ($i = 0; $i < $limit; $i++) {
            if ($preRelease1Ids[$i] === $preRelease2Ids[$i]) {
                continue;
            }

            return $preRelease1Ids[$i] <=> $preRelease2Ids[$i];
        }

        return $preRelease1IdsCount - $preRelease2IdsCount;
    }
}
