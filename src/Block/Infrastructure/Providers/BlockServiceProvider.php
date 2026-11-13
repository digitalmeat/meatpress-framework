<?php

declare(strict_types=1);

namespace Meatpress\Block\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Meatpress\Block\Domain\Contracts\BlockRegistrarInterface;
use Meatpress\Block\Infrastructure\Services\BlockPreview;
use Meatpress\Block\Infrastructure\Services\BlockRegistrar;
use Meatpress\Block\Infrastructure\Services\EditorRuntime;
use Meatpress\Block\Infrastructure\Services\ModuleBlocksRegistrar;
use Meatpress\Block\UI\Console\MakeBlockCommand;
use Meatpress\BlockCategory\Application\Services\BlockCategoryService;
use Meatpress\BlockCategory\Domain\Contracts\BlockCategoryRegistrarInterface;
use Meatpress\BlockCategory\Domain\Contracts\BlockCategoryServiceInterface;
use Meatpress\BlockCategory\Infrastructure\Registrars\BlockCategoryRegistrar;
use Meatpress\BlockPattern\Application\Services\PatternService;
use Meatpress\BlockPattern\Domain\Contracts\PatternCategoryRegistrarInterface;
use Meatpress\BlockPattern\Domain\Contracts\PatternDataExtractorInterface;
use Meatpress\BlockPattern\Domain\Contracts\PatternRegistrarInterface;
use Meatpress\BlockPattern\Domain\Contracts\PatternServiceInterface;
use Meatpress\BlockPattern\Infrastructure\Adapters\WordPressPatternDataExtractor;
use Meatpress\BlockPattern\Infrastructure\Registrars\WordPressPatternCategoryRegistrar;
use Meatpress\BlockPattern\Infrastructure\Registrars\WordPressPatternRegistrar;
use Meatpress\Hook\Domain\Contract\Action;
use Meatpress\Hook\Domain\Contract\Filter;

/**
 * Service provider for Gutenberg blocks, block categories, and block patterns.
 *
 * Consolidates registration of all block-related services:
 * - Block registration and scaffolding
 * - Block category registration
 * - Block pattern registration with category support
 */
class BlockServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerBlockServices();
        $this->registerBlockCategoryServices();
        $this->registerBlockPatternServices();

        if ($this->app->runningInConsole()) {
            $this->commands([MakeBlockCommand::class]);
        }
    }

    public function boot(): void
    {
        $this->publishStubs();

        // Register block categories from configuration
        $this->app->make(BlockCategoryServiceInterface::class)->registerConfiguredCategories();

        $action = $this->app->get(Action::class);

        // Register the blocks of every theme, plugin and module on init. This
        // provider boots before WordPress loads, so the hook is in place in
        // time — unlike one added by a theme or plugin provider.
        $action->add('init', function (): void {
            $this->app->make(EditorRuntime::class)->register();
            $this->app->make(ModuleBlocksRegistrar::class)->registerAll();
        });

        // A block rendered through the core block-renderer route is the
        // editor's preview: its template keeps <InnerBlocks /> for the editor.
        $filter = $this->app->get(Filter::class);
        $preview = $this->app->make(BlockPreview::class);
        $filter->add('rest_request_before_callbacks', $preview->start(...), 10, 3);
        $filter->add('rest_request_after_callbacks', $preview->end(...), 10, 1);

        // Register block patterns on init
        $action->add('init', function (): void {
            $this->app->make(PatternServiceInterface::class)->registerAll();
        });
    }

    private function registerBlockServices(): void
    {
        $this->app->singleton(BlockRegistrar::class);
        $this->app->alias(BlockRegistrar::class, BlockRegistrarInterface::class);
        $this->app->singleton(ModuleBlocksRegistrar::class);
        $this->app->singleton(BlockPreview::class);
        $this->app->singleton(EditorRuntime::class);
    }

    private function registerBlockCategoryServices(): void
    {
        $this->app->bind(BlockCategoryRegistrarInterface::class, BlockCategoryRegistrar::class);
        $this->app->bind(BlockCategoryServiceInterface::class, BlockCategoryService::class);
    }

    private function registerBlockPatternServices(): void
    {
        $this->app->bind(PatternDataExtractorInterface::class, WordPressPatternDataExtractor::class);
        $this->app->bind(PatternCategoryRegistrarInterface::class, WordPressPatternCategoryRegistrar::class);
        $this->app->bind(PatternRegistrarInterface::class, WordPressPatternRegistrar::class);
        $this->app->bind(PatternServiceInterface::class, PatternService::class);
    }

    private function publishStubs(): void
    {
        $stubsPath = dirname(__DIR__, 2).'/stubs';

        if (is_dir($stubsPath)) {
            $this->publishes([
                $stubsPath => base_path('stubs/meatpress-block'),
            ], 'meatpress-block-stubs');
        }
    }
}
