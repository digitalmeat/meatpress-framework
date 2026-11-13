<?php

declare(strict_types=1);

namespace Meatpress\Role\Infrastructure\Laravel;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Meatpress\Doctor\Infrastructure\Providers\DoctorServiceProvider;
use Meatpress\Hook\Domain\Contract\Action;
use Meatpress\Hook\Domain\Contract\Filter;
use Meatpress\Role\Application\Services\CapabilityOwnerReader;
use Meatpress\Role\Application\Services\RoleDeclarer;
use Meatpress\Role\Application\Services\RoleDefinitionBuilder;
use Meatpress\Role\Application\Services\RoleRegistry;
use Meatpress\Role\Domain\Contracts\RoleStoreInterface;
use Meatpress\Role\Domain\Contracts\RoleUsageInterface;
use Meatpress\Role\Domain\Services\PostTypeCapabilityMap;
use Meatpress\Role\Domain\Services\RoleCompiler;
use Meatpress\Role\Infrastructure\Adapters\WordPressLateRefresh;
use Meatpress\Role\Infrastructure\Adapters\WordPressRoleInjector;
use Meatpress\Role\Infrastructure\Adapters\WordPressRoleLabelTranslator;
use Meatpress\Role\Infrastructure\Adapters\WordPressRoleStore;
use Meatpress\Role\Infrastructure\Adapters\WordPressRoleUsage;
use Meatpress\Role\Infrastructure\Laravel\Console\RoleDumpCommand;
use Meatpress\Role\Infrastructure\Laravel\Console\RoleImportCommand;
use Meatpress\Role\Infrastructure\Laravel\Console\RoleListCommand;
use Meatpress\Role\Infrastructure\Laravel\Console\RoleMakeCommand;
use Meatpress\Role\Infrastructure\Laravel\Console\RolePruneCommand;
use Meatpress\Role\Infrastructure\Laravel\Console\RoleShowCommand;
use Meatpress\Role\Infrastructure\Services\RoleDiscovery;
use Psr\Log\LoggerInterface;

/**
 * Roles declared in code: `#[Role]`, `#[ModifyRole]`, `#[CapabilitySet]`,
 * injected into WordPress on `wp_roles_init`; the `role:` middleware and the
 * `@role` directive.
 *
 * Must boot before WordPress loads (before the WordPress service provider), so
 * that the injector is subscribed before the first `WP_Roles` exists.
 */
class RoleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/roles.php', 'roles');

        $this->app->singleton(CapabilityOwnerReader::class);
        $this->app->singleton(PostTypeCapabilityMap::class);
        $this->app->singleton(RoleCompiler::class);
        $this->app->singleton(WordPressRoleLabelTranslator::class);
        $this->app->singleton(RoleUsageInterface::class, WordPressRoleUsage::class);
        $this->app->singleton(RoleStoreInterface::class, WordPressRoleStore::class);

        $this->app->singleton(RoleDefinitionBuilder::class, fn (Application $app): RoleDefinitionBuilder => new RoleDefinitionBuilder(
            $app->make(CapabilityOwnerReader::class),
            $this->superRoles(),
        ));

        // Explicit: the registry checks the fluent declarations with this builder.
        $this->app->singleton(RoleRegistry::class, fn (Application $app): RoleRegistry => new RoleRegistry(
            $app->make(CapabilityOwnerReader::class),
            $app->make(PostTypeCapabilityMap::class),
            $app->make(RoleDefinitionBuilder::class),
        ));

        $this->app->singleton(RoleDeclarer::class, fn (Application $app): RoleDeclarer => new RoleDeclarer(
            $app->make(RoleRegistry::class),
            (new WordPressLateRefresh($app->make(WordPressRoleInjector::class)))->__invoke(...),
        ));

        $this->app->singleton(WordPressRoleInjector::class, fn (Application $app): WordPressRoleInjector => new WordPressRoleInjector(
            $app->make(RoleRegistry::class),
            $app->make(RoleCompiler::class),
            $this->superRoles(),
            $app->make(LoggerInterface::class),
        ));

        $this->app->singleton(RoleDiscovery::class, fn (Application $app): RoleDiscovery => new RoleDiscovery(
            $app->make(RoleDefinitionBuilder::class),
            $app->make(RoleRegistry::class),
            $app->make(WordPressRoleInjector::class),
            $app->make(LoggerInterface::class),
        ));

        if ($this->app->runningInConsole()) {
            $this->commands([RoleMakeCommand::class, RoleListCommand::class, RoleShowCommand::class, RolePruneCommand::class, RoleImportCommand::class, RoleDumpCommand::class]);
        }
    }

    public function boot(): void
    {
        // A check of meatpress:doctor and Site Health; tagged on boot, so it comes after the framework's own
        $this->app->tag([RolesCheck::class], DoctorServiceProvider::CHECKS_TAG);

        $action = $this->app->make(Action::class);
        $injector = $this->app->make(WordPressRoleInjector::class);

        // Early priority: listeners after it see the roles as the code declares them.
        $action->add('wp_roles_init', $injector->inject(...), 1);
        $action->add('init', $injector->reportWarnings(...), PHP_INT_MAX);

        // An alias the application already gives to another middleware is kept.
        $this->callAfterResolving('router', static function (Router $router): void {
            if (! array_key_exists('role', $router->getMiddleware())) {
                $router->aliasMiddleware('role', EnsureUserHasRole::class);
            }
        });

        // Once every provider has booted, to replace the @role of Sage Directives.
        $this->app->booted(function (): void {
            $this->callAfterResolving('blade.compiler', static function (BladeCompiler $blade): void {
                $blade->directive('role', new RoleDirective);
                $blade->directive('endrole', static fn (): string => '<?php endif; ?>');
            });
        });

        $this->app->make(Filter::class)->add('gettext_with_context_default', $this->app->make(WordPressRoleLabelTranslator::class)->translate(...), 10, 3);
    }

    /**
     * @return list<string>
     */
    private function superRoles(): array
    {
        return array_values((array) $this->app->make('config')->get('roles.super_roles', ['administrator']));
    }
}
