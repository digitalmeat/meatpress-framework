<?php

declare(strict_types=1);

namespace Meatpress\Application\Infrastructure\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Meatpress\Application\Domain\Contracts\ConsoleDetectorInterface;
use Meatpress\Application\Infrastructure\Services\LaravelConsoleDetector;

/**
 * Service provider for binding console detection interfaces to implementations.
 */
class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     */
    public function register(): void
    {
        $this->app->singleton(ConsoleDetectorInterface::class, fn (Application $app): LaravelConsoleDetector => new LaravelConsoleDetector($app));
    }
}
