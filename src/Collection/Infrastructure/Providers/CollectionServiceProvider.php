<?php

declare(strict_types=1);

namespace Meatpress\Collection\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Meatpress\Collection\Domain\Contracts\CollectionFactoryInterface;
use Meatpress\Collection\Infrastructure\Services\LaravelCollectionFactory;

/**
 * Service provider for Collection module bindings.
 */
class CollectionServiceProvider extends ServiceProvider
{
    /**
     * Register Collection module bindings.
     */
    public function register(): void
    {
        $this->app->bind(CollectionFactoryInterface::class, LaravelCollectionFactory::class);
    }
}
