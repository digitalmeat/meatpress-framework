<?php

declare(strict_types=1);

namespace Meatpress\PostType\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Meatpress\PostType\Application\Services\PostTypeService;
use Meatpress\PostType\Domain\Contracts\PostTypeFactoryInterface;
use Meatpress\PostType\Domain\Contracts\PostTypeRegistryInterface;
use Meatpress\PostType\Domain\Contracts\PostTypeRepositoryInterface;
use Meatpress\PostType\Domain\Contracts\PostTypeServiceInterface;
use Meatpress\PostType\Infrastructure\Adapters\WordPressPostTypeRegistry;
use Meatpress\PostType\Infrastructure\Factories\PostTypeFactory;
use Meatpress\PostType\Infrastructure\Repositories\PostTypeRepository;
use Meatpress\PostType\Infrastructure\Services\PostTypeDiscovery;
use Meatpress\PostType\UI\Console\PostTypeMakeCommand;

/**
 * Service provider for post type functionality.
 *
 * This provider registers all the necessary services, factories, and repositories
 * following hexagonal architecture principles and dependency injection patterns.
 */
class PostTypeServiceProvider extends ServiceProvider
{
    /**
     * Register the post type services.
     */
    public function register(): void
    {
        // Bind interfaces to implementations
        $this->app->singleton(PostTypeFactoryInterface::class, PostTypeFactory::class);
        $this->app->singleton(PostTypeRegistryInterface::class, WordPressPostTypeRegistry::class);

        // Register the repository
        $this->app->singleton(PostTypeRepositoryInterface::class, fn ($app): PostTypeRepository => new PostTypeRepository(
            $app->make(PostTypeRegistryInterface::class)
        ));

        // Register the PostTypeService with interface binding
        $this->app->singleton(PostTypeServiceInterface::class, fn ($app): PostTypeService => new PostTypeService(
            $app->make(PostTypeFactoryInterface::class),
            $app->make(PostTypeRegistryInterface::class)
        ));

        // Also bind concrete class for backward compatibility
        $this->app->singleton(PostTypeService::class, fn ($app) => $app->make(PostTypeServiceInterface::class));

        // Register PostType Discovery
        $this->app->singleton(PostTypeDiscovery::class, fn ($app): PostTypeDiscovery => new PostTypeDiscovery(
            $app->make(PostTypeServiceInterface::class)
        ));

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                PostTypeMakeCommand::class,
            ]);
        }
    }
}
