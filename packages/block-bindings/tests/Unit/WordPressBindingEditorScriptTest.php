<?php

declare(strict_types=1);

use Brain\Monkey\Functions;
use Meatpress\BlockBinding\Application\Services\BindingSourceRegistry;
use Meatpress\BlockBinding\Infrastructure\Adapters\WordPressBindingEditorScript;
use Meatpress\BlockBinding\Infrastructure\Services\BindingEditorData;
use Meatpress\Meta\Application\Services\MetaSchemaRepository;

it('prints the editor data before the script, on a handle depending on wp-blocks', function (): void {
    $calls = [];
    Functions\when('wp_json_encode')->alias(fn (mixed $data, int $flags = 0): string => json_encode($data, $flags));
    Functions\when('wp_register_script')->alias(function (string $handle, mixed $src, array $deps) use (&$calls): bool {
        $calls[] = ['register', $handle, $src, $deps];

        return true;
    });
    Functions\when('wp_add_inline_script')->alias(function (string $handle, string $script, string $position = 'after') use (&$calls): bool {
        $calls[] = ['inline', $position, $script];

        return true;
    });
    Functions\when('wp_enqueue_script')->alias(function (string $handle) use (&$calls): void {
        $calls[] = ['enqueue', $handle];
    });

    (new WordPressBindingEditorScript(new BindingEditorData(new BindingSourceRegistry, new MetaSchemaRepository, [])))->enqueue();

    expect($calls[0])->toBe(['register', 'meatpress-block-bindings', false, ['wp-api-fetch', 'wp-block-editor', 'wp-blocks', 'wp-data']])
        ->and($calls[1][1])->toBe('before')
        ->and($calls[1][2])->toStartWith('window.meatpressBlockBindings = {"route":"meatpress/v1/block-bindings/resolve"')
        ->and($calls[2][1])->toBe('after')
        ->and($calls[2][2])->toStartWith('/**')
        ->and($calls[3])->toBe(['enqueue', 'meatpress-block-bindings']);
});
