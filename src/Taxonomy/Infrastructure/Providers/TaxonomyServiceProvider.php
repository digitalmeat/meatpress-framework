<?php

declare(strict_types=1);

namespace Meatpress\Taxonomy\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Meatpress\Taxonomy\Application\Services\TaxonomyService;
use Meatpress\Taxonomy\Domain\Contracts\LabelTranslatorInterface;
use Meatpress\Taxonomy\Domain\Contracts\TaxonomyFactoryInterface;
use Meatpress\Taxonomy\Domain\Contracts\TaxonomyRegistryInterface;
use Meatpress\Taxonomy\Domain\Contracts\TaxonomyRepositoryInterface;
use Meatpress\Taxonomy\Domain\Contracts\TaxonomyServiceInterface;
use Meatpress\Taxonomy\Domain\Models\AbstractTaxonomy;
use Meatpress\Taxonomy\Infrastructure\Adapters\WordPressTaxonomyRegistry;
use Meatpress\Taxonomy\Infrastructure\Factories\TaxonomyFactory;
use Meatpress\Taxonomy\Infrastructure\Repositories\TaxonomyRepository;
use Meatpress\Taxonomy\Infrastructure\Services\TaxonomyDiscovery;
use Meatpress\Taxonomy\Infrastructure\Services\WordPressLabelTranslator;
use Meatpress\Taxonomy\UI\Console\TaxonomyMakeCommand;

/**
 * Service provider for taxonomy functionality.
 *
 * This provider registers all the necessary services, factories, and repositories
 * following hexagonal architecture principles and dependency injection patterns.
 */
class TaxonomyServiceProvider extends ServiceProvider
{
    /**
     * Register the taxonomy services.
     */
    public function register(): void
    {
        // Bind interfaces to implementations
        $this->app->singleton(TaxonomyFactoryInterface::class, TaxonomyFactory::class);
        $this->app->singleton(TaxonomyRegistryInterface::class, WordPressTaxonomyRegistry::class);
        $this->app->singleton(LabelTranslatorInterface::class, WordPressLabelTranslator::class);

        // Taxonomy classes are built without arguments: hand their default
        // labels the translator once, rather than through every constructor
        AbstractTaxonomy::useLabelTranslator($this->app->make(LabelTranslatorInterface::class));

        // Register the repository
        $this->app->singleton(TaxonomyRepositoryInterface::class, fn ($app): TaxonomyRepository => new TaxonomyRepository(
            $app->make(TaxonomyRegistryInterface::class)
        ));

        // Register the TaxonomyService with interface binding
        $this->app->singleton(TaxonomyServiceInterface::class, fn ($app): TaxonomyService => new TaxonomyService(
            $app->make(TaxonomyFactoryInterface::class),
            $app->make(TaxonomyRegistryInterface::class)
        ));

        // Also bind concrete class for backward compatibility
        $this->app->singleton(TaxonomyService::class, fn ($app) => $app->make(TaxonomyServiceInterface::class));

        // Register Taxonomy Discovery
        $this->app->singleton(TaxonomyDiscovery::class, fn ($app): TaxonomyDiscovery => new TaxonomyDiscovery(
            $app->make(TaxonomyServiceInterface::class)
        ));

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                TaxonomyMakeCommand::class,
            ]);
        }
    }
}
