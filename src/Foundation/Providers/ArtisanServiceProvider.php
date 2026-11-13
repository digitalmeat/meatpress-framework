<?php

declare(strict_types=1);

namespace Meatpress\Foundation\Providers;

use Illuminate\Support\ServiceProvider;
use Meatpress\Foundation\Console\Commands\MakeModelCommand;

/**
 * Service provider for artisan commands.
 */
class ArtisanServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeModelCommand::class,
            ]);
        }
    }
}
