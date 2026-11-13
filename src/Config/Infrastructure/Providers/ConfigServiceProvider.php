<?php

declare(strict_types=1);

namespace Meatpress\Config\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Meatpress\Config\Domain\Contracts\ConfigRepositoryInterface;
use Meatpress\Config\Infrastructure\Services\LaravelConfigRepository;

/**
 * Service provider for Config module bindings.
 */
class ConfigServiceProvider extends ServiceProvider
{
    /**
     * Register Config module bindings.
     */
    public function register(): void
    {
        $this->app->bind(ConfigRepositoryInterface::class, LaravelConfigRepository::class);
    }
}
