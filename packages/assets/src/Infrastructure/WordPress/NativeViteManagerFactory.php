<?php

declare(strict_types=1);

namespace Meatpress\Asset\Infrastructure\WordPress;

use Meatpress\Asset\Application\Services\MissingBuilds;
use Meatpress\Asset\Domain\Contracts\ViteManagerFactory;
use Meatpress\Asset\Domain\Contracts\ViteManagerInterface;
use Meatpress\Asset\Infrastructure\Repositories\AssetContainer;

/**
 * Resolves a container from its own manifest and hot file.
 */
final readonly class NativeViteManagerFactory implements ViteManagerFactory
{
    public function __construct(private ?MissingBuilds $missingBuilds = null) {}

    public function make(AssetContainer $container): ViteManagerInterface
    {
        return new NativeViteManager($container, $this->missingBuilds);
    }
}
