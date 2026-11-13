<?php

declare(strict_types=1);

use Brain\Monkey\Functions;
use Meatpress\BlockBinding\Application\Services\BindingResolver;
use Meatpress\BlockBinding\Application\Services\BindingSourceBuilder;
use Meatpress\BlockBinding\Application\Services\BindingSourceRegistry;
use Meatpress\BlockBinding\Infrastructure\Adapters\WordPressBindingRegistry;
use Meatpress\BlockBinding\Infrastructure\Services\BindingFormatter;
use Meatpress\BlockBinding\Infrastructure\Services\BlockBindingDiscovery;
use Meatpress\BlockBinding\Infrastructure\Sources\PostMetaSource;
use Meatpress\BlockBinding\Infrastructure\Sources\TypedMetaReader;
use Meatpress\BlockBinding\Tests\Fixtures\ArrayMetaStore;
use Meatpress\BlockBinding\Tests\Fixtures\Concert;
use Meatpress\BlockBinding\Tests\Fixtures\EventBinding;
use Meatpress\BlockBinding\Tests\Fixtures\FakePresenter;
use Meatpress\BlockBinding\Tests\Fixtures\FakeVisibility;
use Meatpress\Discovery\Domain\Models\DiscoveryLocation;
use Meatpress\Hook\Adapter\Out\WordPress\Action;
use Meatpress\Meta\Application\Services\MetaAccessor;
use Meatpress\Meta\Application\Services\MetaSchemaBuilder;
use Meatpress\Meta\Application\Services\MetaSchemaRepository;
use Meatpress\Meta\Domain\Services\MetaValueCaster;
use Psr\Container\ContainerInterface;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;

require_once dirname(__DIR__).'/Fixtures/blocks.php';

/**
 * The wiring the README shows for a project without Laravel: a PSR-11 container that knows the sources, the
 * resolver on top, the sources registered with WordPress, and a project source found by discovery.
 */
it('declares a source, registers it with WordPress and resolves a bound value with nothing but its own classes', function (): void {
    $registered = [];
    Functions\when('did_action')->justReturn(1);
    Functions\when('get_block_bindings_source')->justReturn(null);
    Functions\when('number_format_i18n')->alias(fn (float|int $number, int $decimals = 0): string => number_format((float) $number, $decimals, ',', ' '));
    Functions\when('register_block_bindings_source')->alias(function (string $name, array $args) use (&$registered): void {
        $registered[$name] = $args;
    });

    $schemas = new MetaSchemaRepository;
    $schemas->add((new MetaSchemaBuilder)->build(Concert::class));
    $meta = new MetaAccessor($schemas, new MetaSchemaBuilder, new ArrayMetaStore(['post' => [7 => ['capacity' => '1200']]]), new MetaValueCaster);
    $services = [
        PostMetaSource::class => fn () => new PostMetaSource(new TypedMetaReader($schemas, $meta), new BindingFormatter(new FakePresenter)),
        EventBinding::class => fn () => new EventBinding,
    ];
    $container = new class($services) implements ContainerInterface
    {
        public function __construct(private readonly array $services) {}

        public function get(string $id): mixed
        {
            return ($this->services[$id])();
        }

        public function has(string $id): bool
        {
            return isset($this->services[$id]);
        }
    };

    $builder = new BindingSourceBuilder;
    $sources = new BindingSourceRegistry;
    $registry = new WordPressBindingRegistry(new Action, new BindingResolver($container, new FakeVisibility, new FakePresenter, $meta));

    $discovery = new BlockBindingDiscovery($builder, $sources, $registry);
    $discovery->discover(new DiscoveryLocation('Meatpress\\BlockBinding\\Tests\\', __DIR__), DiscoveredClass::fromReflection(new ReflectionClass(EventBinding::class)));
    $discovery->apply();

    $shipped = $builder->build(PostMetaSource::class);
    $sources->add($shipped);
    $registry->register($shipped);

    $block = boundParagraph(['postId' => 7, 'postType' => 'concert']);

    expect(array_keys($registered))->toBe(['acme/event', 'meatpress/post-meta'])
        ->and($registered['meatpress/post-meta']['get_value_callback'](['key' => 'capacity'], $block, 'content'))->toBe('1 200')
        ->and($registered['acme/event']['get_value_callback'](['field' => 'sold_out'], boundParagraph(), 'content'))->toBe('Yes');
});
