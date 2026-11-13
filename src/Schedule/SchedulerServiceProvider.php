<?php

declare(strict_types=1);

namespace Meatpress\Schedule;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Meatpress\Doctor\Infrastructure\Providers\DoctorServiceProvider;
use Meatpress\Schedule\Application\Services\ScheduleStatus;
use Meatpress\Schedule\Infrastructure\Checks\ScheduleDriverCheck;
use Meatpress\Schedule\Infrastructure\Drivers\LaravelQueueDriver;
use Meatpress\Schedule\Infrastructure\Services\LateEventRecovery;
use Meatpress\Schedule\Infrastructure\Services\MigrationNotice;
use Meatpress\Schedule\Infrastructure\Services\ScheduleDiscovery;
use Meatpress\Schedule\Schedule as ScheduleApi;
use Meatpress\Schedule\UI\Console\ScheduleListCommand;
use Meatpress\Schedule\UI\Console\ScheduleMigrateCommand;
use Meatpress\Schedule\UI\Console\ScheduleRecoverCommand;

/**
 * Service provider for scheduled events: the driver of the site, the commands
 * and the checks around it.
 */
class SchedulerServiceProvider extends ServiceProvider
{
    /**
     * Register scheduler services.
     */
    public function register(): void
    {
        $this->registerDomainContracts();

        $this->mergeConfigFrom(__DIR__.'/../../config/schedule.php', 'schedule');
        $this->app->singleton(ScheduleStatus::class, fn (Application $app): ScheduleStatus => new ScheduleStatus($app->make(Repository::class)));

        // Before anything creates the engine: MEATPRESS_SCHEDULE_DRIVER may name it
        ScheduleApi::extend(LaravelQueueDriver::NAME, fn (): LaravelQueueDriver => $this->app->make(LaravelQueueDriver::class));
        ScheduleApi::useContainer($this->app);
        $this->chooseDriver($this->app->make(Repository::class));

        if ($this->app->runningInConsole()) {
            $this->commands([ScheduleListCommand::class, ScheduleMigrateCommand::class, ScheduleRecoverCommand::class]);
        }
    }

    /**
     * Bootstrap scheduler services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/schedule.php' => config_path('schedule.php'),
        ], 'meatpress-schedule');

        // A check of meatpress:doctor and Site Health; tagged on boot, so it comes after the framework's own
        $this->app->tag([ScheduleDriverCheck::class], DoctorServiceProvider::CHECKS_TAG);
        $this->app->make(MigrationNotice::class)->register();

        $this->app->booted(function (): void {
            // The driver was chosen when the application started; WordPress can take its hooks by now
            ScheduleApi::boot();
            $this->recoverLateEvents();
        });
    }

    /**
     * Choose the driver of the site, WP-Cron when config/schedule.php names one that does not exist (meatpress:doctor says so).
     */
    private function chooseDriver(Repository $config): void
    {
        $driver = $config->get('schedule.driver', 'wp-cron');

        try {
            ScheduleApi::driver(is_string($driver) && $driver !== '' ? $driver : 'wp-cron');
        } catch (\InvalidArgumentException) {
            ScheduleApi::driver('wp-cron');
        }

        $protected = $config->get('schedule.protected_hooks', []);
        ScheduleApi::engine()->protect(...array_map(strval(...), is_array($protected) ? $protected : []));
    }

    /**
     * Register domain contracts with their infrastructure implementations.
     */
    private function registerDomainContracts(): void
    {
        $this->app->singleton(ScheduleDiscovery::class, fn (): ScheduleDiscovery => new ScheduleDiscovery);
        $this->app->singleton(LaravelQueueDriver::class);
        $this->app->singleton(LateEventRecovery::class);
    }

    /**
     * Look for events of the "laravel" driver whose job was lost, when the request ends, at most every five minutes.
     */
    private function recoverLateEvents(): void
    {
        if (! function_exists('add_action') || ScheduleApi::engine()->driverName() !== LaravelQueueDriver::NAME) {
            return;
        }

        add_action('shutdown', function (): void {
            if ($this->app->make(LaravelQueueDriver::class)->available()) {
                $this->app->make(LateEventRecovery::class)->recover();
            }
        });
    }
}
