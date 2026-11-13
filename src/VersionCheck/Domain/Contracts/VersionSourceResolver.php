<?php

declare(strict_types=1);

namespace Meatpress\VersionCheck\Domain\Contracts;

/**
 * Finds where a package's releases are read.
 */
interface VersionSourceResolver
{
    public function for(string $package): VersionSource;
}
