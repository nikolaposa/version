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

        if ($version1->isPreRelease() && $version2->isPreRelease()) {
            return $version1->getPreRelease()->compareTo($version2->getPreRelease());
        }

        // invert: pre-release version has lower precedence than a normal version
        return -1 * ($version1->isPreRelease() <=> $version2->isPreRelease());
    }
}
