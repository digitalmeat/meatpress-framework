<?php

declare(strict_types=1);

namespace Meatpress\Application\Infrastructure\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Meatpress\Application\Domain\Contracts\DebugDetectorInterface;
use Meatpress\Application\Infrastructure\Services\LaravelDebugDetector;

/**
 * Service provider for debug detection.
 */
class DebugServiceProvider extends ServiceProvider
{
    /**
     * Register debug detector services.
     */
    public function register(): void
    {
        $this->app->singleton(DebugDetectorInterface::class, fn (Application $app): LaravelDebugDetector => new LaravelDebugDetector($app));
    }
}
