<?php

declare(strict_types=1);

namespace Version\Comparison;

use Version\Version;

final class SemverComparator implements Comparator
{
    public function compare(Version $version1, Version $version2): int
    {
        if (0 !== ($majorCompareResult = ($version1->getMajor() <=> $version2->getMajor()))) {
            return $majorCompareResult;
        }

        if (0 !== ($minorCompareResult = ($version1->getMinor() <=> $version2->getMinor()))) {
            return $minorCompareResult;
        }

        if (0 !== ($patchCompareResult = ($version1->getPatch() <=> $version2->getPatch()))) {
            return $patchCompareResult;
        }

        return $this->compareMeta($version1, $version2);
    }

    private function compareMeta(Version $version1, Version $version2): int
    {
        $v1IsPreRelease = $version1->isPreRelease();
        $v2IsPreRelease = $version2->isPreRelease();

        if ($v1IsPreRelease xor $v2IsPreRelease) {
            return !$v1IsPreRelease
                ? 1 // normal version has greater precedence than a pre-release version version
                : -1; // pre-release version has lower precedence than a normal version
        }

        return $version1->getPreRelease()->compareTo($version2->getPreRelease());
    }
}
