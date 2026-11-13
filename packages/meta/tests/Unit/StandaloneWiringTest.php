<?php

declare(strict_types=1);

use Brain\Monkey\Functions;
use Meatpress\Discovery\Domain\Models\DiscoveryLocation;
use Meatpress\Hook\Adapter\Out\WordPress\Action;
use Meatpress\Meta\Application\Services\MetaAccessor;
use Meatpress\Meta\Application\Services\MetaSchemaBuilder;
use Meatpress\Meta\Application\Services\MetaSchemaRepository;
use Meatpress\Meta\Domain\Services\MetaValueCaster;
use Meatpress\Meta\Infrastructure\Adapters\WordPressMetaRegistry;
use Meatpress\Meta\Infrastructure\Adapters\WordPressMetaStore;
use Meatpress\Meta\Infrastructure\Services\MetaDiscovery;
use Meatpress\Meta\Tests\Fixtures\Event;
use Meatpress\Meta\Tests\Fixtures\EventStatus;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;

/**
 * The wiring the README shows for a project without Laravel: the pieces the framework's provider builds,
 * built by hand, and the declaration → registration → typed read and write path through them.
 */
it('declares, registers and reads typed meta with nothing but its own classes', function (): void {
    $stored = [];
    Functions\when('did_action')->justReturn(1);
    Functions\when('add_post_type_support')->justReturn();
    Functions\when('register_meta')->justReturn(true);
    Functions\when('wp_slash')->alias(fn (mixed $value): mixed => $value);
    Functions\when('get_metadata_raw')->alias(function (string $type, int $id, string $key) use (&$stored): mixed {
        return $stored["$type:$id:$key"] ?? '';
    });
    Functions\when('update_metadata')->alias(function (string $type, int $id, string $key, mixed $value) use (&$stored): void {
        $stored["$type:$id:$key"] = $value;
    });

    $caster = new MetaValueCaster;
    $schemas = new MetaSchemaRepository;
    $builder = new MetaSchemaBuilder;

    $discovery = new MetaDiscovery($builder, $schemas, new WordPressMetaRegistry(new Action, $caster));
    $discovery->discover(new DiscoveryLocation('Meatpress\\Meta\\Tests\\', __DIR__), DiscoveredClass::fromReflection(new ReflectionClass(Event::class)));
    $discovery->apply();

    $meta = new MetaAccessor($schemas, $builder, new WordPressMetaStore, $caster);
    $meta->of(Event::class, 42)->fill(['capacity' => 250, 'soldOut' => true, 'status' => EventStatus::Published])->save();

    $event = $meta->of(Event::class, 42);

    expect($stored)->toHaveKeys(['post:42:capacity', 'post:42:sold_out', 'post:42:status'])
        ->and($event->capacity)->toBe(250)
        ->and($event->soldOut)->toBeTrue()
        ->and($event->status)->toBe(EventStatus::Published)
        ->and($meta->schemaFor('post', 'event'))->toHaveCount(1);
});
