<?php

declare(strict_types=1);

use Meatpress\Theme\Domain\Contracts\ContainerInterface;
use Meatpress\Theme\Domain\Models\ThemeInitializer;
use Mockery as m;

require_once __DIR__.'/../helpers.php';

describe('ComponentFactory', function (): void {
    it(/**
     * @throws ReflectionException
     */ 'injects ServiceLocator into ThemeComponent (without constructor)', function (): void {
        $mockApp = m::mock(ContainerInterface::class);
        $ref = new ReflectionClass(ThemeInitializer::class);
        $instance = $ref->newInstanceWithoutConstructor();
        $refProp = $ref->getProperty('app');
        $refProp->setValue($instance, $mockApp);

        expect($refProp->getValue($instance))->toBe($mockApp);
    });
});
