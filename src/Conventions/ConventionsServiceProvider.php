<?php

declare(strict_types=1);

namespace Meatpress\Conventions;

use Illuminate\Support\ServiceProvider;
use Meatpress\Conventions\Console\ActivatePluginsCommand;
use Meatpress\Conventions\Console\GenerateSaltsCommand;
use Meatpress\Conventions\Hooks\AcfLocalJson;
use Meatpress\Conventions\Hooks\CleanHead;
use Meatpress\Conventions\Hooks\SearchableCustomFields;
use Meatpress\Conventions\Hooks\SearchEngineVisibility;
use Meatpress\Hook\Domain\Contract\Action;
use Meatpress\Hook\Domain\Contract\Filter;

/**
 * Registers the MEAT Code conventions shared by every Meatpress site.
 *
 * Attribute discovery does not scan vendor/, so the hooks are registered
 * here through the framework's hook services, each behind its meatpress.*
 * configuration switch.
 */
final class ConventionsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/meatpress.php', 'meatpress');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/meatpress.php' => config_path('meatpress.php'),
        ], 'meatpress-config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ActivatePluginsCommand::class,
                GenerateSaltsCommand::class,
            ]);
        }

        $this->registerHooks(
            $this->app->make(Action::class),
            $this->app->make(Filter::class),
        );
    }

    private function registerHooks(Action $action, Filter $filter): void
    {
        $config = $this->app->make('config');

        if ($config->get('meatpress.noindex_outside_production')) {
            $filter->add('wp_robots', [new SearchEngineVisibility, 'filterRobots']);
        }

        if ($config->get('meatpress.searchable_custom_fields.enabled')) {
            $searchable = new SearchableCustomFields(
                array_values($config->get('meatpress.searchable_custom_fields.meta_key_patterns', [])),
                array_values($config->get('meatpress.searchable_custom_fields.ignored_post_types', [])),
            );

            $action->add('save_post', [$searchable, 'syncIntoContent'], 10, 2);
        }

        $cleanHead = new CleanHead(
            (bool) $config->get('meatpress.clean_head.block_styles'),
            (bool) $config->get('meatpress.clean_head.global_styles'),
            (bool) $config->get('meatpress.clean_head.emojis'),
        );

        // Unhooked right before WordPress enqueues them, so it works whenever
        // this provider boots: "init" may already have fired at that point.
        $action->add('wp_enqueue_scripts', [$cleanHead, 'unhookCoreAssets'], 0);
        $action->add('admin_init', [$cleanHead, 'unhookCoreAssets']);
        $action->add('wp_enqueue_scripts', [$cleanHead, 'dequeueBlockStyles'], 100);

        if ($config->get('meatpress.acf_local_json.enabled')) {
            $acf = new AcfLocalJson((string) $config->get('meatpress.acf_local_json.directory', 'acf-json'));

            $filter->add('acf/settings/save_json', [$acf, 'savePath']);
            $filter->add('acf/settings/load_json', [$acf, 'loadPaths']);
        }
    }
}
