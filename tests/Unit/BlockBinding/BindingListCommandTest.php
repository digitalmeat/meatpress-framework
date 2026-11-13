<?php

declare(strict_types=1);

use Illuminate\Console\Attributes\Signature;
use Illuminate\Container\Container;
use Illuminate\Foundation\Application;
use Meatpress\BlockBinding\Application\Services\BindingSourceBuilder;
use Meatpress\BlockBinding\Application\Services\BindingSourceRegistry;
use Meatpress\BlockBinding\Infrastructure\Laravel\BlockBindingServiceProvider;
use Meatpress\BlockBinding\Infrastructure\Laravel\Console\BindingListCommand;
use Meatpress\BlockBinding\Infrastructure\Services\BindingEditorData;
use Meatpress\Meta\Application\Services\MetaSchemaBuilder;
use Meatpress\Meta\Application\Services\MetaSchemaRepository;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Tests\Unit\BlockBinding\Fixtures\Concert;
use Tests\Unit\BlockBinding\Fixtures\EventBinding;

/**
 * @param  array<string, mixed>  $input
 */
function runBindingList(array $input = [], ?array $blocks = null): string
{
    $app = new Application(sys_get_temp_dir());
    $sources = new BindingSourceRegistry;

    foreach ([EventBinding::class, ...BlockBindingServiceProvider::SOURCES] as $class) {
        $sources->add((new BindingSourceBuilder)->build($class));
    }

    $schemas = new MetaSchemaRepository;
    $schemas->add((new MetaSchemaBuilder)->build(Concert::class));

    $app->instance(BindingSourceRegistry::class, $sources);
    $app->instance(BindingEditorData::class, new BindingEditorData($sources, $schemas, ['blogname']));

    $command = new #[Signature('meatpress:binding:list {--json}')] class($blocks) extends BindingListCommand
    {
        public function __construct(private readonly ?array $blocks)
        {
            parent::__construct();
        }

        protected function bindableBlocks(): ?array
        {
            return $this->blocks;
        }
    };
    $command->setLaravel($app);

    $output = new BufferedOutput;
    $command->run(new ArrayInput($input), $output);
    Container::setInstance(new Container);

    return $output->fetch();
}

it('lists the sources, what they offer and the bindable blocks, as JSON', function (): void {
    $json = json_decode(runBindingList(['--json' => true], ['core/paragraph' => ['content']]), true);
    $event = $json['sources'][0];

    expect(array_column($json['sources'], 'name'))->toBe(['acme/event', 'meatpress/post-meta', 'meatpress/term-meta', 'meatpress/author-meta', 'meatpress/option'])
        ->and($event['post_types'])->toBe(['event'])
        ->and($event['offers'][0])->toBe(['args' => 'field: remaining_seats', 'label' => 'Remaining seats', 'for' => ''])
        ->and(array_column($json['sources'][1]['offers'], 'args'))->toContain('key: capacity', 'key: cover_image_id')
        ->and(count(array_filter($json['sources'][1]['offers'], fn (array $offer): bool => $offer['args'] === 'key: cover_image_id')))->toBe(1)
        ->and($json['sources'][4]['offers'])->toBe([['args' => 'name: blogname', 'label' => 'Blogname', 'for' => '']])
        ->and($json['bindable_blocks'])->toBe(['core/paragraph' => ['content']]);
});

it('prints a readable list', function (): void {
    $output = runBindingList([], ['acme/card' => ['title', 'ctaUrl']]);

    expect($output)->toContain('5 Meatpress binding source(s)')
        ->toContain('1 bindable block(s)')
        ->toContain('title, ctaUrl')
        ->toContain('acme/event')
        ->toContain('field: remaining_seats')
        ->toContain('key: starts_at');
});
