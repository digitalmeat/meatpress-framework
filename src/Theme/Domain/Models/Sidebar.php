<?php

declare(strict_types=1);

namespace Meatpress\Theme\Domain\Models;

use Meatpress\Config\Domain\Contracts\ConfigRepositoryInterface;
use Meatpress\Hook\Domain\Contract\Action;
use Meatpress\Services\Translater;
use Meatpress\Theme\Domain\Contracts\ThemeComponent;
use Meatpress\Theme\Domain\Contracts\ThemeFeatureRegistrarInterface;
use Meatpress\Theme\Domain\Support\ThemeConfig;
use Psr\Container\ContainerInterface;

/**
 * Class Sidebar
 *
 * This class is responsible for registering theme sidebars.
 */
class Sidebar implements ThemeComponent
{
    protected Action $action;

    public function __construct(protected ContainerInterface $app, protected ConfigRepositoryInterface $config)
    {
        $this->action = $this->app->get(Action::class);
    }

    public function register(): void
    {
        $this->action->add('widgets_init', $this->registerSidebars(...), 1);
    }

    /**
     * Register all of the site's theme sidebars.
     */
    public function registerSidebars(): void
    {
        $sidebars = (array) ThemeConfig::get('sidebars', []);
        $translater = new Translater($sidebars, 'sidebars');
        $sidebars = $translater->translate(['*.name', '*.description']);

        $features = $this->app->get(ThemeFeatureRegistrarInterface::class);

        foreach ($sidebars as $value) {
            $features->registerSidebar($value);
        }
    }
}
