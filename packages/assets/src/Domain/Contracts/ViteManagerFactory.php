<?php

declare(strict_types=1);

namespace Meatpress\Asset\Domain\Contracts;

use Meatpress\Asset\Infrastructure\Repositories\AssetContainer;

/**
 * Builds the Vite resolver of an asset container.
 *
 * The environment decides what resolves a container: Laravel's Vite in a
 * Meatpress application, the manifest and hot file read directly elsewhere.
 */
interface ViteManagerFactory
{
    /**
     * The resolver of the container: its own hot file, build directory and manifest.
     */
    public function make(AssetContainer $container): ViteManagerInterface;
}
