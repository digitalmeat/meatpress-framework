<?php

declare(strict_types=1);

use Meatpress\Application\Application\Services\ConsoleDetectionService;
use Meatpress\Application\Domain\Contracts\DebugDetectorInterface;
use Meatpress\Discovery\Infrastructure\Providers\DiscoveryServiceProvider;
use Meatpress\Hook\Adapter\Out\WordPress\Action;
use Meatpress\Hook\Adapter\Out\WordPress\Filter;
use Meatpress\Hook\Domain\Contract\Action as ActionContract;
use Meatpress\Hook\Domain\Contract\CallbackResolverInterface;
use Meatpress\Hook\Domain\Contract\Filter as FilterContract;
use Meatpress\Hook\Infrastructure\Providers\HookServiceProvider;
use Meatpress\Hook\Infrastructure\Services\HookDiscovery;

beforeEach(function (): void {
    // HookServiceProvider depends on ConsoleDetectionService
    $this->consoleDetection = Mockery::mock(ConsoleDetectionService::class);
    $this->consoleDetection->shouldReceive('isConsole')->andReturn(false)->byDefault();

    $this->app->instance(ConsoleDetectionService::class, $this->consoleDetection);

    // Register dependencies and provider
    $this->app->singleton(DebugDetectorInterface::class, fn () => Mockery::mock(DebugDetectorInterface::class, [
        'isDebugMode' => false,
    ]));
    $this->app->register(DiscoveryServiceProvider::class);
    $this->app->register(new HookServiceProvider($this->app, $this->consoleDetection));
});

describe('HookServiceProvider', function (): void {
    it('binds Action as singleton', function (): void {
        $action = $this->app->make(Action::class);

        expect($action)->toBeInstanceOf(Action::class);
        expect($this->app->make(Action::class))->toBe($action);
    });

    it('binds Filter as singleton', function (): void {
        $filter = $this->app->make(Filter::class);

        expect($filter)->toBeInstanceOf(Filter::class);
        expect($this->app->make(Filter::class))->toBe($filter);
    });

    it('binds ActionContract to Action implementation', function (): void {
        $action = $this->app->make(ActionContract::class);

        expect($action)->toBeInstanceOf(Action::class);
    });

    it('binds FilterContract to Filter implementation', function (): void {
        $filter = $this->app->make(FilterContract::class);

        expect($filter)->toBeInstanceOf(Filter::class);
    });

    it('binds HookDiscovery as singleton', function (): void {
        $discovery = $this->app->make(HookDiscovery::class);

        expect($discovery)->toBeInstanceOf(HookDiscovery::class);
        expect($this->app->make(HookDiscovery::class))->toBe($discovery);
    });

    it('registers HookDiscovery as singleton for auto-registration', function (): void {
        expect($this->app->bound(HookDiscovery::class))->toBeTrue();
    });

    it('injects CallbackResolver into Action singleton', function (): void {
        $action = $this->app->make(Action::class);
        $reflection = new ReflectionProperty($action, 'callbackResolver');

        expect($reflection->getValue($action))->toBeInstanceOf(CallbackResolverInterface::class);
    });

    it('injects CallbackResolver into Filter singleton', function (): void {
        $filter = $this->app->make(Filter::class);
        $reflection = new ReflectionProperty($filter, 'callbackResolver');

        expect($reflection->getValue($filter))->toBeInstanceOf(CallbackResolverInterface::class);
    });

    it('resolves same Action singleton via Domain contract and Infrastructure class', function (): void {
        $viaConcrete = $this->app->make(Action::class);
        $viaContract = $this->app->make(ActionContract::class);

        expect($viaContract)->toBe($viaConcrete);
    });

    it('resolves same Filter singleton via Domain contract and Infrastructure class', function (): void {
        $viaConcrete = $this->app->make(Filter::class);
        $viaContract = $this->app->make(FilterContract::class);

        expect($viaContract)->toBe($viaConcrete);
    });
});
