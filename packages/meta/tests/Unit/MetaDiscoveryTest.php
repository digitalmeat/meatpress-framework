<?php

declare(strict_types=1);

use Meatpress\Attributes\Meta;
use Meatpress\Colt\Model\Page;
use Meatpress\Colt\Model\Post as ColtPost;
use Meatpress\Discovery\Domain\Models\DiscoveryLocation;
use Meatpress\Meta\Application\Services\MetaSchemaBuilder;
use Meatpress\Meta\Application\Services\MetaSchemaRepository;
use Meatpress\Meta\Domain\Contracts\MetaRegistryInterface;
use Meatpress\Meta\Domain\Models\MetaSchema;
use Meatpress\Meta\Infrastructure\Services\MetaDiscovery;
use Meatpress\Meta\Tests\Fixtures\ArticleExtras;
use Meatpress\Meta\Tests\Fixtures\BookGenre;
use Meatpress\Meta\Tests\Fixtures\CategoryExtras;
use Meatpress\Meta\Tests\Fixtures\Event;
use Meatpress\Meta\Tests\Fixtures\EventExtras;
use Meatpress\Meta\Tests\Fixtures\EventPostModel;
use Meatpress\Meta\Tests\Fixtures\EventStatus;
use Meatpress\Meta\Tests\Fixtures\InvalidArray;
use Meatpress\Meta\Tests\Fixtures\MemberProfile;
use Meatpress\Meta\Tests\Fixtures\NoMeta;
use Meatpress\Meta\Tests\Fixtures\NotADeclaration;
use Meatpress\Meta\Tests\Fixtures\ReviewMeta;
use Psr\Log\LoggerInterface;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;
use Spatie\StructureDiscoverer\Data\DiscoveredEnum;

require_once dirname(__DIR__).'/Fixtures/Invalid.php';

abstract class AbstractMetaDeclaration
{
    #[Meta]
    public string $value = '';
}

beforeEach(function (): void {
    $this->registry = Mockery::mock(MetaRegistryInterface::class);
    $this->schemas = new MetaSchemaRepository;
    $this->logger = Mockery::mock(LoggerInterface::class);
    $this->discovery = new MetaDiscovery(new MetaSchemaBuilder, $this->schemas, $this->registry, $this->logger);
    $this->location = new DiscoveryLocation('Tests\\', __DIR__);

    $this->discover = function (string ...$classes): void {
        foreach ($classes as $class) {
            $this->discovery->discover($this->location, DiscoveredClass::fromReflection(new ReflectionClass($class)));
        }
    };
});

it('keeps the classes declaring meta only', function (): void {
    ($this->discover)(Event::class, BookGenre::class, ArticleExtras::class, CategoryExtras::class, MemberProfile::class, ReviewMeta::class, NotADeclaration::class, AbstractMetaDeclaration::class);
    $this->discovery->discover($this->location, DiscoveredEnum::fromReflection(new ReflectionEnum(EventStatus::class)));

    expect(array_column(iterator_to_array($this->discovery->getItems()), 'class'))->toBe([Event::class, BookGenre::class, ArticleExtras::class, CategoryExtras::class, MemberProfile::class, ReviewMeta::class]);
});

it('stores and registers the schema of each declaring class', function (): void {
    $registered = [];
    $this->registry->shouldReceive('register')->andReturnUsing(function (MetaSchema $schema) use (&$registered): void {
        $registered[] = $schema->declaringClass;
    });
    ($this->discover)(Event::class, BookGenre::class, NoMeta::class);

    $this->discovery->apply();

    expect($registered)->toBe([Event::class, BookGenre::class])
        ->and($this->schemas->forClass(Event::class))->toBeInstanceOf(MetaSchema::class)
        ->and($this->schemas->forClass(NoMeta::class))->toBeNull();
});

it('logs a declaration it cannot register and carries on', function (): void {
    $this->registry->shouldReceive('register')->twice();
    $this->logger->shouldReceive('error')->once()->with(Mockery::pattern('/InvalidArray: .*say what the array holds/'), Mockery::type('array'));
    $this->logger->shouldReceive('error')->once()->with(Mockery::pattern('/EventExtras: The meta key "capacity" of post "event" is declared twice/'), Mockery::type('array'));
    ($this->discover)(Event::class, InvalidArray::class, EventExtras::class, BookGenre::class);

    $this->discovery->apply();

    expect(array_keys($this->schemas->failures()))->toBe([InvalidArray::class, EventExtras::class])
        ->and($this->schemas->failures()[InvalidArray::class])->toContain('say what the array holds');
});

it('binds the post models of the project to their post type', function (): void {
    ($this->discover)(EventPostModel::class, Page::class);

    $this->discovery->apply();

    expect((new ReflectionProperty(ColtPost::class, 'postTypes'))->getValue())->toHaveKey('fixture_event', EventPostModel::class);

    ColtPost::clearRegisteredPostTypes();
});

it('identifies itself as meta', function (): void {
    expect($this->discovery->getIdentifier())->toBe('meta');
});
