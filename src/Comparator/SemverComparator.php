<?php

/**
 * This file is part of the Version package.
 *
 * Copyright (c) Nikola Posa <posa.nikola@gmail.com>
 *
 * For full copyright and license information, please refer to the LICENSE file,
 * located at the package root folder.
 */

namespace Version\Comparator;

use Version\Version;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
final class SemverComparator implements ComparatorInterface
{
    /**
     * {@inheritDoc}
     */
    public function compare(Version $version1, Version $version2)
    {
        if ($version1->getMajor() > $version2->getMajor()) {
            return 1;
        }

        if ($version1->getMajor() < $version2->getMajor()) {
            return -1;
        }

        if ($version1->getMinor() > $version2->getMinor()) {
            return 1;
        }

        if ($version1->getMinor() < $version2->getMinor()) {
            return -1;
        }

        if ($version1->getPatch() > $version2->getPatch()) {
            return 1;
        }

        if ($version1->getPatch() < $version2->getPatch()) {
            return -1;
        }

        return $this->compareMeta($version1, $version2);
    }

    private function compareMeta(Version $version1, Version $version2)
    {
        if (!$version1->isPreRelease() && $version2->isPreRelease()) {
            // normal version has greater precedence than a pre-release version version
            return 1;
        }

        if ($version1->isPreRelease() && !$version2->isPreRelease()) {
            // pre-release version has lower precedence than a normal version
            return -1;
        }

        if ($version1->isPreRelease() && $version2->isPreRelease()) {
            $result = $version1->getPreRelease()->compareTo($version2->getPreRelease());

            if ($result > 0) {
                return 1;
            }

            if ($result < 0) {
                return -1;
            }
        }

        return 0;
    }
}
