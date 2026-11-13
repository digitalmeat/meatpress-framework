<?php

declare(strict_types=1);

namespace Meatpress\Permalink\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Meatpress\Hook\Domain\Contract\Action;
use Meatpress\Hook\Domain\Contract\Filter;
use Meatpress\Permalink\Domain\Contracts\UrlNormalizerInterface;
use Meatpress\Permalink\Domain\Services\UrlNormalizer;
use Meatpress\Permalink\Infrastructure\Services\PermalinkManager;
use Meatpress\Support\Uri;

/**
 * Service provider for permalink management and URL normalization.
 *
 * Registers the URL normalizer and permalink manager as singletons,
 * then hooks into WordPress's URL generation and canonical redirect
 * systems to enforce Meatpress's no-trailing-slash convention.
 *
 * Three WordPress hooks are registered:
 * - `user_trailingslashit` — removes trailing slashes from all generated URLs at the source
 * - `redirect_canonical` — normalizes canonical redirect URLs for external access
 * - `permalink_structure_changed` — ensures stored permalink structure has no trailing slash
 */
class PermalinkServiceProvider extends ServiceProvider
{
    /**
     * Register URL normalization services.
     */
    public function register(): void
    {
        $this->app->singleton(UrlNormalizerInterface::class, fn (): UrlNormalizer => new UrlNormalizer(
            new Uri
        ));

        $this->app->singleton(PermalinkManager::class, fn ($app): PermalinkManager => new PermalinkManager(
            $app->make(UrlNormalizerInterface::class)
        ));
    }

    /**
     * Bootstrap permalink hooks into WordPress.
     *
     * Registers filters and actions using the domain hook contracts
     * injected from the container, following Meatpress's hook system conventions.
     */
    public function boot(Filter $filter, Action $action): void
    {
        $manager = $this->app->make(PermalinkManager::class);

        $filter->add(
            'user_trailingslashit',
            $manager->handleUserTrailingSlash(...),
            10
        );

        $filter->add(
            'redirect_canonical',
            $manager->handleCanonicalRedirect(...)
        );

        $action->add(
            'permalink_structure_changed',
            function (string $old, string $new) use ($manager): void {
                $manager->updateStructure($new);
            },
            90
        );
    }
}
