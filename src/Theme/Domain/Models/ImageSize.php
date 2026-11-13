<?php

declare(strict_types=1);

/**
 * Class ImageSize
 *
 * This class is responsible for registering image sizes in a WordPress theme.
 */

namespace Meatpress\Theme\Domain\Models;

use Meatpress\Config\Domain\Contracts\ConfigRepositoryInterface;
use Meatpress\Hook\Domain\Contract\Action;
use Meatpress\Theme\Domain\Contracts\ThemeComponent;
use Meatpress\Theme\Domain\Contracts\ThemeFeatureRegistrarInterface;
use Meatpress\Theme\Domain\Support\ThemeConfig;
use Psr\Container\ContainerInterface;

/**
 * Represents a class for handling image sizes.
 */
class ImageSize implements ThemeComponent
{
    protected Action $action;

    public function __construct(
        protected ContainerInterface $app,
        protected ConfigRepositoryInterface $config
    ) {
        $this->action = $this->app->get(Action::class);
    }

    public function register(): void
    {
        $this->action->add('after_setup_theme', $this->addImageSize(...));
    }

    /**
     * Register the image sizes.
     */
    public function addImageSize(): void
    {
        $images = ThemeConfig::get('images', []);

        $features = $this->app->get(ThemeFeatureRegistrarInterface::class);

        foreach ($images as $name => $sizes) {
            $features->addImageSize(
                $name,
                $sizes[0] ?? 0,
                $sizes[1] ?? 0,
                $sizes[2] ?? false
            );
        }
    }
}
