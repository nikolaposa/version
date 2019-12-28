<?php

declare(strict_types=1);

namespace Version\Comparison;

use Version\Extension\PreRelease;
use Version\Version;

final class SemverComparator implements Comparator
{
    public function compare(Version $version1, Version $version2): int
    {
        if (0 !== ($numberComparisonResult = $this->compareNumbers($version1, $version2))) {
            return $numberComparisonResult;
        }

        if ($version1->isPreRelease() && $version2->isPreRelease()) {
            return $this->comparePreReleases($version1->getPreRelease(), $version2->getPreRelease());
        }

        return $this->resolvePreReleasePrecedence($version1, $version2);
    }

    private function compareNumbers(Version $version1, Version $version2): int
    {
        return [$version1->getMajor(), $version1->getMinor(), $version1->getPatch()] <=> [$version2->getMajor(), $version2->getMinor(), $version2->getPatch()];
    }

    private function comparePreReleases(PreRelease $preRelease1, PreRelease $preRelease2): int
    {
        $preRelease1Ids = $preRelease1->getIdentifiers();
        $preRelease2Ids = $preRelease2->getIdentifiers();

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

    private function resolvePreReleasePrecedence(Version $version1, Version $version2): int
    {
        //pre-release version has lower precedence than a normal version
        return -1 * ($version1->isPreRelease() <=> $version2->isPreRelease());
    }
}
