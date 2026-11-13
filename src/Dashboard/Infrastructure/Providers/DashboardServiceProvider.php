<?php

declare(strict_types=1);

namespace Meatpress\Dashboard\Infrastructure\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Meatpress\Dashboard\Domain\Contracts\WordPressEnvironmentInterface;
use Meatpress\Dashboard\Domain\Services\SystemInfoCollector;
use Meatpress\Dashboard\Infrastructure\Adapters\WordPressEnvironment;
use Meatpress\Dashboard\UI\Console\StatusCommand;
use Meatpress\Dashboard\UI\Http\DashboardController;
use Meatpress\Discovery\Application\Services\DiscoveryManager;
use Meatpress\Hook\Domain\Contract\Action;
use Meatpress\VersionCheck\Domain\Services\VersionComparator;

/**
 * Service provider for the Meatpress admin dashboard.
 *
 * Registers the dashboard admin page under the WordPress admin menu
 * and the `meatpress:status` Artisan command. Shows a notification badge
 * on the menu when a framework update is available.
 */
class DashboardServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(WordPressEnvironmentInterface::class, WordPressEnvironment::class);

        $this->app->singleton(SystemInfoCollector::class, fn ($app): SystemInfoCollector => new SystemInfoCollector(
            $app->make(VersionComparator::class),
            $app->make(DiscoveryManager::class),
            $app,
            Application::VERSION,
            $app->make(WordPressEnvironmentInterface::class),
        ));

        if ($this->app->runningInConsole()) {
            $this->commands([StatusCommand::class]);
        }
    }

    public function boot(Action $action): void
    {
        if (! function_exists('is_admin') || ! is_admin()) {
            return;
        }

        $action->add('admin_menu', function (): void {
            $menuTitle = $this->buildMenuTitle();

            add_submenu_page(
                'tools.php',
                __('Meatpress', 'meatpress'),
                $menuTitle,
                'manage_options',
                'meatpress-dashboard',
                $this->app->make(DashboardController::class)
            );
        });
    }

    /**
     * Build the menu title with an optional notification badge.
     *
     * Shows a counter badge (like Site Health) when a framework update
     * is available, drawing attention without being intrusive.
     */
    private function buildMenuTitle(): string
    {
        $title = __('Meatpress', 'meatpress');
        $notifications = $this->countNotifications();

        if ($notifications === 0) {
            return $title;
        }

        return sprintf(
            '%s <span class="update-plugins count-%d"><span class="plugin-count">%d</span></span>',
            $title,
            $notifications,
            $notifications
        );
    }

    /**
     * Count the number of active notifications (update available, etc.).
     */
    private function countNotifications(): int
    {
        $count = 0;

        try {
            if ($this->app->make(VersionComparator::class)->isUpdateAvailable()) {
                $count++;
            }
        } catch (\Throwable) {
            // Silently ignore if version check is unavailable
        }

        return $count;
    }
}
