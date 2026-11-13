<?php

declare(strict_types=1);

namespace Meatpress\Application\Infrastructure\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Meatpress\Application\Domain\Contracts\ConsoleDetectorInterface;
use Meatpress\Application\Domain\Contracts\DebugDetectorInterface;
use Meatpress\Application\Infrastructure\Services\LaravelConsoleDetector;
use Meatpress\Application\Infrastructure\Services\LaravelDebugDetector;

/**
 * Service provider for application detection services (console, debug).
 */
class ApplicationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ConsoleDetectorInterface::class, fn (Application $app): LaravelConsoleDetector => new LaravelConsoleDetector($app));
        $this->app->singleton(DebugDetectorInterface::class, fn (Application $app): LaravelDebugDetector => new LaravelDebugDetector($app));
    }
}
