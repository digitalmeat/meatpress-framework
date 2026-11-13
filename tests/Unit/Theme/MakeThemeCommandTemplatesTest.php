<?php

declare(strict_types=1);

use Meatpress\Theme\UI\Console\MakeThemeCommand;

describe('MakeThemeCommand templates', function (): void {
    $constant = fn (string $name): array => (new ReflectionClassConstant(MakeThemeCommand::class, $name))->getValue();

    it('offers every built-in template in the prompt, and nothing that has no repository', function () use ($constant): void {
        expect(array_keys($constant('TEMPLATE_LABELS')))->toBe(array_keys($constant('TEMPLATES')));
    });

    it('offers Buzz as the magazine template', function () use ($constant): void {
        expect($constant('TEMPLATES'))->toMatchArray([
            'default' => 'meatpress/theme-default',
            'ecommerce' => 'meatpress/theme-apiary',
            'magazine' => 'meatpress/theme-buzz',
        ]);
    });
});
