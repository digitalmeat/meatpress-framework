<?php

declare(strict_types=1);

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Facade;
use Meatpress\Config\Domain\Contracts\ConfigRepositoryInterface;
use Meatpress\Config\Infrastructure\Services\LaravelConfigRepository;

beforeEach(function (): void {
    // Set up a real config Repository and bind it so the Config facade works
    $app = Container::getInstance();
    $this->configRepo = new Repository(['app' => ['name' => 'Meatpress', 'debug' => true]]);
    $app->instance('config', $this->configRepo);
    Facade::setFacadeApplication($app);
    Config::clearResolvedInstances();

    $this->repository = new LaravelConfigRepository;
});

describe('LaravelConfigRepository', function (): void {
    it('implements ConfigRepositoryInterface', function (): void {
        expect($this->repository)->toBeInstanceOf(ConfigRepositoryInterface::class);
    });

    it('delegates get() to Config', function (): void {
        expect($this->repository->get('app.name'))->toBe('Meatpress');
    });

    it('returns default when key is missing', function (): void {
        expect($this->repository->get('app.missing', 'fallback'))->toBe('fallback');
    });

    it('returns null when key is missing without default', function (): void {
        expect($this->repository->get('nonexistent'))->toBeNull();
    });

    it('delegates set() to Config', function (): void {
        $this->repository->set('app.name', 'NewValue');

        expect($this->configRepo->get('app.name'))->toBe('NewValue');
    });

    it('delegates has() to Config for existing key', function (): void {
        expect($this->repository->has('app.name'))->toBeTrue();
    });

    it('delegates has() to Config for missing key', function (): void {
        expect($this->repository->has('nonexistent.key'))->toBeFalse();
    });
});
