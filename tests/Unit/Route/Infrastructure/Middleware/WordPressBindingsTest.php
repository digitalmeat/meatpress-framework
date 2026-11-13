<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Meatpress\Route\Infrastructure\Middleware\WordPressBindings;
use Meatpress\Route\Infrastructure\Models\Route;
use Meatpress\Route\Infrastructure\Services\WordPressRoutingService;

describe('WordPressBindings middleware', function (): void {
    beforeEach(function (): void {
        $this->routingService = Mockery::mock(WordPressRoutingService::class);
        $this->middleware = new WordPressBindings($this->routingService);
    });

    it('adds bindings for WordPress routes', function (): void {
        $route = Mockery::mock(Route::class);
        $route->shouldReceive('hasCondition')->andReturn(true);

        $request = Request::create('/test');
        $request->setRouteResolver(fn () => $route);

        $this->routingService->shouldReceive('bindWordPressParameters')->once()->with($route);

        $result = $this->middleware->handle($request, fn ($req): string => 'response');

        expect($result)->toBe('response');
    });

    it('skips bindings for non-WordPress routes', function (): void {
        $route = Mockery::mock(Route::class);
        $route->shouldReceive('hasCondition')->andReturn(false);

        $request = Request::create('/test');
        $request->setRouteResolver(fn () => $route);

        $this->routingService->shouldNotReceive('bindWordPressParameters');

        $result = $this->middleware->handle($request, fn ($req): string => 'response');

        expect($result)->toBe('response');
    });

    it('handles request without route', function (): void {
        $request = Request::create('/test');
        $request->setRouteResolver(fn (): null => null);

        $this->routingService->shouldNotReceive('bindWordPressParameters');

        $result = $this->middleware->handle($request, fn ($req): string => 'response');

        expect($result)->toBe('response');
    });

    it('skips non-Meatpress route instances', function (): void {
        $route = new stdClass;

        $request = Request::create('/test');
        $request->setRouteResolver(fn (): stdClass => $route);

        $this->routingService->shouldNotReceive('bindWordPressParameters');

        $result = $this->middleware->handle($request, fn ($req): string => 'response');

        expect($result)->toBe('response');
    });
});
