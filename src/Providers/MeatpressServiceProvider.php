<?php

declare(strict_types=1);

namespace Meatpress\Providers;

use Illuminate\Support\ServiceProvider;
use Log1x\SageDirectives\SageDirectivesServiceProvider;
use Meatpress\Ability\Infrastructure\Providers\AbilityServiceProvider;
use Meatpress\Ajax\Infrastructure\Providers\AjaxServiceProvider;
use Meatpress\Application\Infrastructure\Providers\ApplicationServiceProvider;
use Meatpress\Asset\Infrastructure\Providers\AssetServiceProvider;
use Meatpress\Auth\AuthServiceProvider;
use Meatpress\Block\Infrastructure\Providers\BlockServiceProvider;
use Meatpress\BlockBinding\Infrastructure\Laravel\BlockBindingServiceProvider;
use Meatpress\Collection\Infrastructure\Providers\CollectionServiceProvider;
use Meatpress\Config\Infrastructure\Providers\ConfigServiceProvider;
use Meatpress\Dashboard\Infrastructure\Providers\DashboardServiceProvider;
use Meatpress\Discovery\Infrastructure\Providers\DiscoveryServiceProvider;
use Meatpress\Doctor\Infrastructure\Providers\DoctorServiceProvider;
use Meatpress\Events\WordPress\WordPressEventServiceProvider;
use Meatpress\Exceptions\Infrastructure\Providers\ExceptionServiceProvider;
use Meatpress\Foundation\Providers\ArtisanServiceProvider;
use Meatpress\Hashing\HashServiceProvider;
use Meatpress\Hook\Infrastructure\Providers\AsyncServiceProvider;
use Meatpress\Hook\Infrastructure\Providers\HookServiceProvider;
use Meatpress\Logging\Infrastructure\Providers\LoggingServiceProvider;
use Meatpress\Login\Infrastructure\Providers\LoginServiceProvider;
use Meatpress\Mail\WordPressMailServiceProvider;
use Meatpress\Media\Infrastructure\Laravel\MediaServiceProvider;
use Meatpress\Meta\Infrastructure\Laravel\MetaServiceProvider;
use Meatpress\Modules\Infrastructure\Providers\ModuleServiceProvider;
use Meatpress\Option\Infrastructure\Providers\OptionServiceProvider;
use Meatpress\Permalink\Infrastructure\Providers\PermalinkServiceProvider;
use Meatpress\Plugin\Infrastructure\Providers\PluginServiceProvider;
use Meatpress\PostType\Infrastructure\Providers\PostTypeServiceProvider;
use Meatpress\Role\Infrastructure\Laravel\RoleServiceProvider;
use Meatpress\Route\Infrastructure\Providers\RouteServiceProvider;
use Meatpress\Schedule\SchedulerServiceProvider;
use Meatpress\Taxonomy\Infrastructure\Providers\TaxonomyServiceProvider;
use Meatpress\Theme\Infrastructure\Providers\ThemeServiceProvider;
use Meatpress\VersionCheck\Infrastructure\Providers\VersionCheckServiceProvider;
use Meatpress\View\Infrastructure\Providers\TemplateHierarchyServiceProvider;
use Meatpress\View\ViewServiceProvider;
use Meatpress\WordPress\Config\ConstantServiceProvider;
use Meatpress\WordPress\WordPressServiceProvider;
use Meatpress\WpCli\Infrastructure\Providers\WpCliServiceProvider;
use Meatpress\WpRest\Infrastructure\Providers\WpRestAttributeServiceProvider;

/**
 * Main service provider for the Meatpress framework.
 *
 * This provider is responsible for registering all core service providers
 * that power the WordPress integration with Laravel. It handles the initialization
 * of various components like views, authentication, post types, and more.
 */
class MeatpressServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * Registers all core service providers including:
     * - WordPress integration services
     * - Theme and view handling
     * - Authentication and hashing
     * - Content type management (posts, taxonomies)
     * - Asset and Gutenberg integration
     * - Scheduling and job dispatching
     * - ...
     */
    public function register(): void
    {
        // Generic service providers
        $this->app->register(ApplicationServiceProvider::class);
        $this->app->register(ArtisanServiceProvider::class);
        $this->app->register(LoggingServiceProvider::class);
        $this->app->register(DiscoveryServiceProvider::class);
        $this->app->register(ModuleServiceProvider::class);
        $this->app->register(ConstantServiceProvider::class);
        $this->app->register(ViewServiceProvider::class);
        $this->app->register(ExceptionServiceProvider::class);

        $this->app->register(TaxonomyServiceProvider::class);

        $this->app->register(PostTypeServiceProvider::class);
        $this->app->register(MetaServiceProvider::class);
        $this->app->register(RoleServiceProvider::class);
        $this->app->register(MediaServiceProvider::class);
        $this->app->register(BlockBindingServiceProvider::class);

        // WordPress REST API
        $this->app->register(WpRestAttributeServiceProvider::class);

        // WordPress CLI
        $this->app->register(WpCliServiceProvider::class);

        // Shared modules
        $this->app->register(CollectionServiceProvider::class);
        $this->app->register(OptionServiceProvider::class);

        // Block features (blocks, categories, patterns)
        $this->app->register(BlockServiceProvider::class);

        $this->app->register(WordPressMailServiceProvider::class);
        $this->app->register(HookServiceProvider::class);
        $this->app->register(AsyncServiceProvider::class);

        $this->app->register(PermalinkServiceProvider::class);
        $this->app->register(ThemeServiceProvider::class);
        $this->app->register(PluginServiceProvider::class);
        $this->app->register(AssetServiceProvider::class);
        $this->app->register(AjaxServiceProvider::class);
        $this->app->register(AbilityServiceProvider::class);
        $this->app->register(ConfigServiceProvider::class);
        $this->app->register(QueryServiceProvider::class);
        $this->app->register(SageDirectivesServiceProvider::class);
        $this->app->register(WordPressEventServiceProvider::class);
        $this->app->register(TemplateHierarchyServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);

        $this->app->register(SchedulerServiceProvider::class);

        // Authentication service provider
        $this->app->register(AuthServiceProvider::class);

        // Login screen customisation
        $this->app->register(LoginServiceProvider::class);

        // Hashing service provider
        $this->app->register(HashServiceProvider::class);
        $this->app->register(WordPressServiceProvider::class);

        // Version check notifications
        $this->app->register(VersionCheckServiceProvider::class);

        // Dashboard
        $this->app->register(DashboardServiceProvider::class);
        $this->app->register(DoctorServiceProvider::class);
    }

    /**
     * Perform post-registration booting of services.
     *
     * Publishes necessary configuration files and assets to the public directory.
     * This includes the WordPress configuration file that bridges Laravel and WordPress.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../public/wp-config.php' => public_path(),
        ], 'public');

        $this->loadMeatpressTextDomain();
    }

    /**
     * Load the 'meatpress' text domain for framework translations.
     *
     * Checks for user-provided translations in `wp-content/languages/meatpress/`
     * first, then falls back to the framework's bundled translations.
     */
    private function loadMeatpressTextDomain(): void
    {
        if (! function_exists('load_textdomain')) {
            return;
        }

        $locale = determine_locale();
        $moFile = sprintf('meatpress-%s.mo', $locale);

        // User-provided translations take priority
        $loaded = load_textdomain('meatpress', WP_LANG_DIR.'/meatpress/'.$moFile);

        // Fall back to bundled translations
        if (! $loaded) {
            load_textdomain('meatpress', dirname(__DIR__, 2).'/resources/languages/'.$moFile);
        }
    }
}
