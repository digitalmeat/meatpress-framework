<?php

declare(strict_types=1);

namespace Meatpress\Asset\Infrastructure\Laravel;

use Meatpress\Asset\Domain\Contracts\ViteManagerFactory;
use Meatpress\Asset\Domain\Contracts\ViteManagerInterface;
use Meatpress\Asset\Infrastructure\Repositories\AssetContainer;
use Meatpress\Asset\Infrastructure\Services\ViteManager;

/**
 * Resolves a container through Laravel's Vite.
 */
final class LaravelViteManagerFactory implements ViteManagerFactory
{
    public function make(AssetContainer $container): ViteManagerInterface
    {
        return new ViteManager($container);
    }
}
