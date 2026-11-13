<?php

declare(strict_types=1);

namespace Meatpress\Theme\Infrastructure\Providers;

use Illuminate\Contracts\Foundation\Application;
use Meatpress\BlockPattern\UI\PatternComponent;
use Meatpress\Theme\Domain\Exceptions\ThemeSetupException;
use Meatpress\Theme\Domain\Models\ImageSize;
use Meatpress\Theme\Domain\Models\Menus;
use Meatpress\Theme\Domain\Models\Sidebar;
use Meatpress\Theme\Domain\Models\Templates;
use Meatpress\Theme\Domain\Models\ThemeInitializer;
use Meatpress\Theme\Infrastructure\Services\Support;
use Psr\Log\LoggerInterface;

/**
 * Simplified provider for registering and bootstrapping theme components.
 */
class ThemeComponentProvider
{
    /**
     * Theme components to register (order matters).
     */
    protected array $components = [
        ThemeInitializer::class,
        PatternComponent::class,
        Menus::class,
        Support::class,
        Sidebar::class,
        Templates::class,
        ImageSize::class,
    ];

    public function __construct(protected Application $app) {}

    /**
     * Register all theme components.
     */
    public function register(): void
    {
        foreach ($this->components as $component) {
            $this->registerComponent($component);
        }
    }

    /**
     * Register a single component.
     */
    protected function registerComponent(string $component): void
    {
        try {
            if (! $this->app->bound($component)) {
                $this->app->singleton($component);
            }

            $instance = $this->app->make($component);
            $instance->register();
        } catch (\Throwable $throwable) {
            if (config('app.debug')) {
                throw ThemeSetupException::componentFailed($component, $throwable);
            }

            // Log error but continue in production
            try {
                $this->app->make(LoggerInterface::class)->error(
                    sprintf('Theme component registration failed: %s - ', $component).$throwable->getMessage()
                );
            } catch (\Throwable) {
            }
        }
    }

    /**
     * Add a component to the list.
     */
    public function addComponent(string $componentClass): self
    {
        if (! in_array($componentClass, $this->components)) {
            $this->components[] = $componentClass;
        }

        return $this;
    }
}
