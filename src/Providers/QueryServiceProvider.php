<?php

declare(strict_types=1);

namespace Meatpress\Providers;

use Illuminate\Support\ServiceProvider;
use Meatpress\Colt\Model\User as BaseUser;
use Meatpress\Models\Post;
use Meatpress\Models\User;
use Meatpress\Query\DateQuery;
use Meatpress\Query\MetaQuery;
use Meatpress\Query\PostQuery;
use Meatpress\Query\TaxQuery;

/**
 * Service provider for WordPress query functionality.
 *
 * This provider registers various query-related services that handle WordPress
 * query operations, including:
 * - Post queries
 * - Taxonomy queries
 * - Meta queries
 * - Date queries
 */
class QueryServiceProvider extends ServiceProvider
{
    /**
     * Register query-related services.
     *
     * Binds various query classes and utilities to the service container:
     * - Custom user model binding
     * - Post query singleton
     * - Taxonomy query singleton
     * - Meta query singleton
     * - Date query singleton
     * - WordPress loop binding
     * - Current post binding
     */
    public function register(): void
    {
        // User model binding
        $this->app->bind(BaseUser::class, User::class);

        // Query singletons
        $this->app->singleton('wp.query.post', fn (): PostQuery => new PostQuery);
        $this->app->singleton('wp.query.taxonomy', fn (): TaxQuery => new TaxQuery);
        $this->app->singleton('wp.query.meta', fn (): MetaQuery => new MetaQuery);
        $this->app->singleton('wp.query.date', fn (): DateQuery => new DateQuery);

        // Current post binding
        $this->app->singleton(Post::class, fn () => Post::find(get_the_ID()));
    }
}
