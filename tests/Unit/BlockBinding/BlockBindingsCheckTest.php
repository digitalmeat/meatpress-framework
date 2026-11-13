<?php

declare(strict_types=1);

use Brain\Monkey\Functions;
use Illuminate\Filesystem\Filesystem;
use Meatpress\BlockBinding\Application\Services\BindingReferenceInspector;
use Meatpress\BlockBinding\Application\Services\BindingSourceBuilder;
use Meatpress\BlockBinding\Application\Services\BindingSourceRegistry;
use Meatpress\BlockBinding\Infrastructure\Laravel\BlockBindingsCheck;
use Meatpress\Doctor\Domain\Enums\CheckStatus;
use Meatpress\Doctor\Domain\Enums\RunContext;
use Meatpress\Doctor\Infrastructure\Support\ProjectModule;
use Meatpress\Doctor\Infrastructure\Support\ProjectModules;
use Meatpress\Meta\Application\Services\MetaSchemaRepository;
use Tests\Unit\BlockBinding\Fixtures\EventBinding;

/**
 * A block as parse_blocks() returns it, bound to the given sources.
 *
 * @param  array<string, array{source: string, args?: array<string, mixed>}>  $bindings
 * @param  list<array<string, mixed>>  $innerBlocks
 * @return array<string, mixed>
 */
function parsedBlock(string $name, array $bindings = [], array $innerBlocks = []): array
{
    return ['blockName' => $name, 'attrs' => $bindings === [] ? [] : ['metadata' => ['bindings' => $bindings]], 'innerBlocks' => $innerBlocks];
}

beforeEach(function (): void {
    $this->root = sys_get_temp_dir().'/meatpress-binding-check-'.uniqid();
    mkdir($this->root.'/templates', 0777, true);
    mkdir($this->root.'/resources/views/blocks/card', 0777, true);
    $this->theme = new ProjectModule('theme', 'journal', $this->root, $this->root.'/hot', 'build/theme/journal');

    $modules = Mockery::mock(ProjectModules::class);
    $modules->shouldReceive('all')->andReturnUsing(fn (): array => [$this->theme]);
    $sources = new BindingSourceRegistry;
    $sources->add((new BindingSourceBuilder)->build(EventBinding::class));

    $this->check = new BlockBindingsCheck($modules, new BindingReferenceInspector($sources, new MetaSchemaRepository));

    // The template files hold the parsed blocks, as JSON
    Functions\when('did_action')->justReturn(1);
    Functions\when('parse_blocks')->alias(fn (string $content): array => json_decode($content, true));
    Functions\when('get_block_bindings_source')->alias(fn (string $name): ?object => in_array($name, ['acme/event', 'core/post-meta'], true) ? (object) [] : null);
    Functions\when('get_block_bindings_supported_attributes')->alias(fn (string $block): array => ['core/paragraph' => ['content'], 'core/button' => ['url', 'text']][$block] ?? []);
});

afterEach(function (): void {
    (new Filesystem)->deleteDirectory($this->root);
});

it('passes when every binding can show its value', function (): void {
    file_put_contents($this->root.'/templates/single.html', json_encode([
        parsedBlock('core/group', [], [parsedBlock('core/paragraph', ['content' => ['source' => 'acme/event', 'args' => ['field' => 'remaining_seats']]])]),
        parsedBlock('core/paragraph', ['content' => ['source' => 'core/post-meta', 'args' => ['key' => 'anything']]]),
        parsedBlock('core/paragraph', ['__default' => ['source' => 'core/pattern-overrides']]),
    ]));

    $result = $this->check->run(RunContext::Console);

    expect($result->status)->toBe(CheckStatus::Ok)
        ->and($result->summary)->toBe('2 block binding(s), each able to show its value.');
});

it('names each binding that can never show a value, nested ones included', function (): void {
    file_put_contents($this->root.'/templates/single.html', json_encode([
        parsedBlock('core/group', [], [parsedBlock('core/paragraph', ['content' => ['source' => 'acme/event', 'args' => ['field' => 'seats']]])]),
        parsedBlock('core/paragraph', ['content' => ['source' => 'acme/missing']]),
        parsedBlock('core/button', ['linkTarget' => ['source' => 'acme/event', 'args' => ['field' => 'booking_url']]]),
    ]));

    $result = $this->check->run(RunContext::Console);

    expect($result->status)->toBe(CheckStatus::Error)
        ->and($result->details)->toBe([
            'theme journal: templates/single.html — core/paragraph, "content" → acme/event: the field "seats" does not exist; acme/event has the fields "remaining_seats", "booking_url", "capacity", "sold_out", "summary", "nothing", "broken"',
            'theme journal: templates/single.html — core/paragraph, "content": the source "acme/missing" is not registered',
            'theme journal: templates/single.html — core/button, "linkTarget": WordPress does not bind this attribute of core/button',
        ]);
});

it('warns about an attribute WordPress does not bind', function (): void {
    file_put_contents($this->root.'/templates/single.html', json_encode([
        parsedBlock('core/button', ['linkTarget' => ['source' => 'acme/event', 'args' => ['field' => 'booking_url']]]),
    ]));

    expect($this->check->run(RunContext::Console)->status)->toBe(CheckStatus::Warning);
});

it('names a block.json whose meatpress.bindings cannot be honoured', function (): void {
    file_put_contents($this->root.'/resources/views/blocks/card/block.json', json_encode([
        'name' => 'journal/card', 'attributes' => ['title' => ['type' => 'string']], 'meatpress' => ['bindings' => ['title', 'subtitle']], 'render' => 'file:./render.blade.php',
    ]));
    mkdir($this->root.'/resources/views/blocks/static', 0777, true);
    file_put_contents($this->root.'/resources/views/blocks/static/block.json', json_encode(['name' => 'journal/static', 'meatpress' => ['bindings' => ['title']]]));

    expect($this->check->run(RunContext::Console)->details)->toBe([
        'theme journal: resources/views/blocks/card/block.json: meatpress.bindings lists "subtitle", which is not one of its attributes',
        'theme journal: resources/views/blocks/static/block.json: lists meatpress.bindings but has no "render" — only a block rendered on the server can be bound',
    ]);
});

it('waits for WordPress', function (): void {
    Functions\when('did_action')->justReturn(0);

    expect($this->check->run(RunContext::Console)->status)->toBe(CheckStatus::Skipped);
});
