<?php

declare(strict_types=1);

namespace Meatpress\Logging\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Meatpress\Logging\Application\Services\WordPressErrorLoggingService;
use Meatpress\Logging\Domain\Contracts\WordPressErrorHookRegistrarInterface;
use Meatpress\Logging\Domain\Contracts\WordPressErrorLoggerInterface;
use Meatpress\Logging\Domain\Services\WordPressErrorHandler;
use Meatpress\Logging\Infrastructure\Adapters\LaravelWordPressErrorLogger;
use Meatpress\Logging\Infrastructure\Services\WordPressErrorHookRegistrar;

class LoggingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerLoggingChannels();
        $this->registerServices();
    }

    public function boot(): void
    {
        $hookRegistrar = $this->app->make(WordPressErrorHookRegistrarInterface::class);
        $hookRegistrar->registerErrorHandlers();
    }

    private function registerLoggingChannels(): void
    {
        $existingChannels = config('logging.channels', []);
        $meatpressChannels = $this->getDefaultLoggingChannels();
        $mergedChannels = array_merge($meatpressChannels, $existingChannels);
        config(['logging.channels' => $mergedChannels]);
    }

    private function registerServices(): void
    {
        $this->app->bind(WordPressErrorLoggerInterface::class, LaravelWordPressErrorLogger::class);
        $this->app->bind(WordPressErrorHookRegistrarInterface::class, WordPressErrorHookRegistrar::class);

        $this->app->singleton(WordPressErrorHandler::class);
        $this->app->singleton(WordPressErrorLoggingService::class);
    }

    protected function getDefaultLoggingChannels(): array
    {
        return [
            'wordpress' => [
                'driver' => 'single',
                'path' => storage_path('logs/wordpress.log'),
                'level' => config('logging.channels.wordpress.level', 'debug'),
                'days' => config('logging.channels.wordpress.days', 7),
                'replace_placeholders' => true,
            ],
        ];
    }
}
