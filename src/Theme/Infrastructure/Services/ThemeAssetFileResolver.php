<?php

declare(strict_types=1);

namespace Meatpress\Theme\Infrastructure\Services;

use Meatpress\Asset\Infrastructure\Services\AssetFile;
use Meatpress\Theme\Domain\Contracts\ThemeAssetUrlResolverInterface;

/**
 * Answers the built URL of a theme file through the `theme` asset container.
 */
class ThemeAssetFileResolver implements ThemeAssetUrlResolverInterface
{
    public function assetUrl(string $file): string
    {
        return (string) (new AssetFile($file))->from('theme');
    }
}
