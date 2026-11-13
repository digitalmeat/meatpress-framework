<?php

declare(strict_types=1);

use Meatpress\BlockBinding\Application\Services\BindingReferenceInspector;
use Meatpress\BlockBinding\Application\Services\BindingSourceBuilder;
use Meatpress\BlockBinding\Application\Services\BindingSourceRegistry;
use Meatpress\BlockBinding\Infrastructure\Sources\AuthorMetaSource;
use Meatpress\BlockBinding\Infrastructure\Sources\OptionSource;
use Meatpress\BlockBinding\Infrastructure\Sources\PostMetaSource;
use Meatpress\BlockBinding\Infrastructure\Sources\TermMetaSource;
use Meatpress\BlockBinding\Tests\Fixtures\ArtistProfile;
use Meatpress\BlockBinding\Tests\Fixtures\Concert;
use Meatpress\BlockBinding\Tests\Fixtures\EventBinding;
use Meatpress\BlockBinding\Tests\Fixtures\InvokableBinding;
use Meatpress\BlockBinding\Tests\Fixtures\VenueExtras;
use Meatpress\Meta\Application\Services\MetaSchemaBuilder;
use Meatpress\Meta\Application\Services\MetaSchemaRepository;

beforeEach(function (): void {
    $sources = new BindingSourceRegistry;

    foreach ([PostMetaSource::class, TermMetaSource::class, AuthorMetaSource::class, OptionSource::class, EventBinding::class, InvokableBinding::class] as $class) {
        $sources->add((new BindingSourceBuilder)->build($class));
    }

    $schemas = new MetaSchemaRepository;

    foreach ([Concert::class, ArtistProfile::class, VenueExtras::class] as $class) {
        $schemas->add((new MetaSchemaBuilder)->build($class));
    }

    $this->inspector = new BindingReferenceInspector($sources, $schemas, ['blogdescription']);
});

it('accepts a binding that can show its value', function (string $source, array $args): void {
    expect($this->inspector->problem($source, $args))->toBeNull();
})->with([
    'a field' => ['acme/event', ['field' => 'remaining_seats']],
    'an invokable source' => ['acme/echo', ['say' => 'hi']],
    'a post meta in REST' => ['meatpress/post-meta', ['key' => 'capacity']],
    'a term meta in REST' => ['meatpress/term-meta', ['key' => 'city']],
    'a public user meta' => ['meatpress/author-meta', ['key' => 'stage_name']],
    'a listed option' => ['meatpress/option', ['name' => 'blogdescription']],
]);

it('says why a binding would leave its block unchanged', function (string $source, array $args, string $problem): void {
    expect($this->inspector->problem($source, $args))->toBe($problem);
})->with([
    'unknown field' => ['acme/event', ['field' => 'seats'], 'the field "seats" does not exist; acme/event has the fields "remaining_seats", "booking_url", "capacity", "sold_out", "summary", "nothing", "broken"'],
    'no field' => ['acme/event', [], 'no "field" argument; acme/event has the fields "remaining_seats", "booking_url", "capacity", "sold_out", "summary", "nothing", "broken"'],
    'undeclared meta' => ['meatpress/post-meta', ['key' => 'nope'], 'no #[Meta] declares the post meta "nope"'],
    'meta out of REST' => ['meatpress/post-meta', ['key' => 'backstage_code'], 'the meta "backstage_code" is never shown: it is not exposed in REST (showInRest: true) or its key is protected'],
    'protected meta' => ['meatpress/post-meta', ['key' => '_promoter_fee'], 'the meta "_promoter_fee" is never shown: it is not exposed in REST (showInRest: true) or its key is protected'],
    'private user meta' => ['meatpress/author-meta', ['key' => 'phone'], 'the meta "phone" is never shown: it is not marked #[Meta(public: true)]'],
    'no key' => ['meatpress/term-meta', [], 'no "key" argument'],
    'unlisted option' => ['meatpress/option', ['name' => 'admin_email'], 'the option "admin_email" is not listed in block-bindings.options'],
]);

it('leaves the sources it does not know to WordPress', function (): void {
    expect($this->inspector->knows('core/post-meta'))->toBeFalse()
        ->and($this->inspector->problem('core/post-meta', ['key' => 'anything']))->toBeNull()
        ->and($this->inspector->knows('acme/event'))->toBeTrue();
});
