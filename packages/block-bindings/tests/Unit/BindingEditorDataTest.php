<?php

declare(strict_types=1);

use Meatpress\BlockBinding\Application\Services\BindingSourceBuilder;
use Meatpress\BlockBinding\Application\Services\BindingSourceRegistry;
use Meatpress\BlockBinding\Infrastructure\Services\BindingEditorData;
use Meatpress\BlockBinding\Infrastructure\Sources\AuthorMetaSource;
use Meatpress\BlockBinding\Infrastructure\Sources\OptionSource;
use Meatpress\BlockBinding\Infrastructure\Sources\PostMetaSource;
use Meatpress\BlockBinding\Infrastructure\Sources\TermMetaSource;
use Meatpress\BlockBinding\Tests\Fixtures\ArtistProfile;
use Meatpress\BlockBinding\Tests\Fixtures\Concert;
use Meatpress\BlockBinding\Tests\Fixtures\EventBinding;
use Meatpress\BlockBinding\Tests\Fixtures\VenueExtras;
use Meatpress\Meta\Application\Services\MetaSchemaBuilder;
use Meatpress\Meta\Application\Services\MetaSchemaRepository;

beforeEach(function (): void {
    $sources = new BindingSourceRegistry;

    foreach ([PostMetaSource::class, TermMetaSource::class, AuthorMetaSource::class, OptionSource::class, EventBinding::class] as $class) {
        $sources->add((new BindingSourceBuilder)->build($class));
    }

    $schemas = new MetaSchemaRepository;

    foreach ([Concert::class, ArtistProfile::class, VenueExtras::class] as $class) {
        $schemas->add((new MetaSchemaBuilder)->build($class));
    }

    $this->data = (new BindingEditorData($sources, $schemas, ['blogdescription']))->toArray();
});

it('gives the editor the route and the fields of a #[BlockBinding] source', function (): void {
    expect($this->data['route'])->toBe('meatpress/v1/block-bindings/resolve')
        ->and($this->data['sources']['acme/event']['postTypes'])->toBe(['event'])
        ->and($this->data['sources']['acme/event']['fields'][0])->toBe(['label' => 'Remaining seats', 'args' => ['field' => 'remaining_seats'], 'type' => 'string']);
});

it('offers the post meta a binding may show, by post type', function (): void {
    $fields = $this->data['sources']['meatpress/post-meta'];
    $keys = array_map(static fn (array $field): string => $field['args']['key'].':'.$field['type'], $fields['fields']['concert']);

    expect($fields['subtype'])->toBe('postType')
        ->and($keys)->toContain('capacity:string', 'starts_at:string', 'cover_image_id:string', 'cover_image_id:number')
        ->not->toContain('backstage_code:string', '_promoter_fee:string')
        ->and($fields['fields']['concert'][0]['label'])->toBe('Starts At');
});

it('offers the public user meta, the term meta and the listed options', function (): void {
    expect(array_column(array_column($this->data['sources']['meatpress/author-meta']['fields'][''], 'args'), 'key'))->toBe(['stage_name'])
        ->and($this->data['sources']['meatpress/term-meta']['fields']['venue'][0]['args'])->toBe(['key' => 'city'])
        ->and($this->data['sources']['meatpress/option']['fields'])->toBe([['label' => 'Blogdescription', 'args' => ['name' => 'blogdescription'], 'type' => 'string']]);
});
