<?php

declare(strict_types=1);

namespace Meatpress\Doctor\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Meatpress\Doctor\Application\Services\Doctor;
use Meatpress\Doctor\Infrastructure\Checks\AppUrlCheck;
use Meatpress\Doctor\Infrastructure\Checks\AssetBuildCheck;
use Meatpress\Doctor\Infrastructure\Checks\BlockRegistrationCheck;
use Meatpress\Doctor\Infrastructure\Checks\BlockThemeRoutesCheck;
use Meatpress\Doctor\Infrastructure\Checks\DevelopmentCachesCheck;
use Meatpress\Doctor\Infrastructure\Checks\DiscoveryCacheCheck;
use Meatpress\Doctor\Infrastructure\Checks\EnvironmentFileCheck;
use Meatpress\Doctor\Infrastructure\Checks\LegacyBlocksDirectoryCheck;
use Meatpress\Doctor\Infrastructure\Checks\PatchesLockCheck;
use Meatpress\Doctor\Infrastructure\Checks\PatternCacheCheck;
use Meatpress\Doctor\Infrastructure\Checks\PatternFilesCheck;
use Meatpress\Doctor\Infrastructure\Checks\PublicRootCheck;
use Meatpress\Doctor\Infrastructure\Checks\SymlinkedDirectoryCheck;
use Meatpress\Doctor\Infrastructure\Checks\TemplatePlaceholdersCheck;
use Meatpress\Doctor\Infrastructure\Checks\WooCommercePackageCheck;
use Meatpress\Doctor\Infrastructure\Checks\WordPressCorePatchCheck;
use Meatpress\Doctor\Infrastructure\Checks\WpRocketPackageCheck;
use Meatpress\Doctor\UI\Console\DoctorCommand;
use Meatpress\Doctor\UI\Http\SiteHealthTests;
use Meatpress\Hook\Domain\Contract\Filter;

/**
 * meatpress:doctor and its Site Health tests: one list of checks, two places to read them.
 */
class DoctorServiceProvider extends ServiceProvider
{
    public const string CHECKS_TAG = 'meatpress.doctor.checks';

    /**
     * In the order a developer should read them: the install, then what the project builds.
     *
     * @var list<class-string>
     */
    private const array CHECKS = [
        WordPressCorePatchCheck::class,
        PatchesLockCheck::class,
        EnvironmentFileCheck::class,
        AppUrlCheck::class,
        DevelopmentCachesCheck::class,
        DiscoveryCacheCheck::class,
        AssetBuildCheck::class,
        SymlinkedDirectoryCheck::class,
        TemplatePlaceholdersCheck::class,
        PatternFilesCheck::class,
        PatternCacheCheck::class,
        BlockThemeRoutesCheck::class,
        LegacyBlocksDirectoryCheck::class,
        BlockRegistrationCheck::class,
        PublicRootCheck::class,
        WooCommercePackageCheck::class,
        WpRocketPackageCheck::class,
    ];

    public function register(): void
    {
        $this->app->tag(self::CHECKS, self::CHECKS_TAG);
        $this->app->singleton(Doctor::class, fn ($app): Doctor => new Doctor($app->tagged(self::CHECKS_TAG)));

        if ($this->app->runningInConsole()) {
            $this->commands([DoctorCommand::class]);
        }
    }

    public function boot(Filter $filter): void
    {
        $filter->add('site_status_tests', fn (array $tests): array => $this->app->make(SiteHealthTests::class)->register($tests));
    }
}
