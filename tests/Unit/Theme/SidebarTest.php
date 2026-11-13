<?php

declare(strict_types=1);

use Meatpress\Config\Domain\Contracts\ConfigRepositoryInterface;
use Meatpress\Hook\Domain\Contract\Action;
use Meatpress\Theme\Domain\Contracts\ThemeFeatureRegistrarInterface;
use Meatpress\Theme\Domain\Models\Sidebar;
use Meatpress\Theme\Domain\Support\ThemeConfig;
use Mockery as m;
use Psr\Container\ContainerInterface;

require_once __DIR__.'/../helpers.php';

describe('Sidebar', function (): void {
    it('resolves Application from Laravel container', function (): void {
        $mockAction = m::mock(Action::class);
        $mockContainer = m::mock(ContainerInterface::class);
        $mockConfig = m::mock(ConfigRepositoryInterface::class);

        // Container should provide Action when requested
        $mockContainer->shouldReceive('get')
            ->with(Action::class)
            ->andReturn($mockAction);

        $sidebar = new Sidebar($mockContainer, $mockConfig);
        $ref = new ReflectionProperty($sidebar, 'app');

        expect($ref->getValue($sidebar))->toBe($mockContainer);
    });

    it('registers every configured sidebar through the feature registrar', function (): void {
        $config = m::mock(ConfigRepositoryInterface::class);
        $config->shouldReceive('get')->with('theme.sidebars', [])->andReturn([
            ['id' => 'primary', 'name' => 'Primary'],
            ['id' => 'footer', 'name' => 'Footer'],
        ]);
        ThemeConfig::setRepository($config);

        $registered = [];
        $features = m::mock(ThemeFeatureRegistrarInterface::class);
        $features->shouldReceive('registerSidebar')->andReturnUsing(function (array $args) use (&$registered): void {
            $registered[] = $args['id'];
        });

        $container = m::mock(ContainerInterface::class);
        $container->shouldReceive('get')->with(Action::class)->andReturn(m::mock(Action::class));
        $container->shouldReceive('get')->with(ThemeFeatureRegistrarInterface::class)->andReturn($features);

        (new Sidebar($container, $config))->registerSidebars();

        ThemeConfig::reset();

        expect($registered)->toBe(['primary', 'footer']);
    });
});
