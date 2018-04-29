<?php

declare(strict_types=1);

namespace Version;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
trait VersionAwareTrait
{
    /**
     * @var Version
     */
    protected $version;

    public function setVersion(Version $version) : void
    {
        $this->version = $version;
    }

    public function getVersion() : ?Version
    {
        return $this->version;
    }
}
