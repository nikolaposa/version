<?php

declare(strict_types=1);

namespace Version;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
interface VersionAwareInterface
{
    public function setVersion(Version $version) : void;

    public function getVersion() : Version;
}
