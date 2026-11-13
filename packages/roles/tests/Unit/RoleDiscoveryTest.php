<?php

declare(strict_types=1);

use Meatpress\Attributes\Role;
use Meatpress\Discovery\Domain\Models\DiscoveryLocation;
use Meatpress\Role\Application\Services\CapabilityOwnerReader;
use Meatpress\Role\Application\Services\RoleDefinitionBuilder;
use Meatpress\Role\Application\Services\RoleRegistry;
use Meatpress\Role\Domain\Services\PostTypeCapabilityMap;
use Meatpress\Role\Domain\Services\RoleCompiler;
use Meatpress\Role\Infrastructure\Adapters\WordPressRoleInjector;
use Meatpress\Role\Infrastructure\Services\RoleDiscovery;
use Meatpress\Role\Tests\Fixtures\Article;
use Meatpress\Role\Tests\Fixtures\EditorAdjustments;
use Meatpress\Role\Tests\Fixtures\Event;
use Meatpress\Role\Tests\Fixtures\EventCap;
use Meatpress\Role\Tests\Fixtures\EventManager;
use Meatpress\Role\Tests\Fixtures\Genre;
use Meatpress\Role\Tests\Fixtures\RedeclaresCoreRole;
use Psr\Log\LoggerInterface;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;
use Spatie\StructureDiscoverer\Data\DiscoveredEnum;

require_once dirname(__DIR__).'/Fixtures/Invalid.php';
require_once dirname(__DIR__).'/Fixtures/wordpress-roles.php';

#[Role('abstract_role')]
abstract class AbstractRoleDeclaration {}

#[Role('not_an_enum_set')]
enum RoleOnAnEnum: string
{
    case Value = 'value';
}

beforeEach(function (): void {
    $this->registry = new RoleRegistry(new CapabilityOwnerReader, new PostTypeCapabilityMap);
    $this->logger = Mockery::mock(LoggerInterface::class);
    $this->discovery = new RoleDiscovery(
        new RoleDefinitionBuilder(new CapabilityOwnerReader),
        $this->registry,
        new WordPressRoleInjector($this->registry, new RoleCompiler),
        $this->logger,
    );
    $this->location = new DiscoveryLocation('Tests\\', __DIR__);
    $this->discover = function (string ...$classes): void {
        foreach ($classes as $class) {
            $structure = enum_exists($class)
                ? DiscoveredEnum::fromReflection(new ReflectionEnum($class))
                : DiscoveredClass::fromReflection(new ReflectionClass($class));
            $this->discovery->discover($this->location, $structure);
        }
    };
});

afterEach(function (): void {
    unset($GLOBALS['wp_roles']);
});

it('keeps roles, modifications, capability sets, post types and taxonomies', function (): void {
    ($this->discover)(EventManager::class, EditorAdjustments::class, EventCap::class, Event::class, Genre::class, AbstractRoleDeclaration::class, RoleOnAnEnum::class, stdClass::class);

    expect(iterator_to_array($this->discovery->getItems()))->toBe([
        ['class' => EventManager::class, 'kind' => 'role'],
        ['class' => EditorAdjustments::class, 'kind' => 'role'],
        ['class' => EventCap::class, 'kind' => 'capabilitySet'],
        ['class' => Event::class, 'kind' => 'postType'],
        ['class' => Genre::class, 'kind' => 'taxonomy'],
    ]);
});

it('fills the registry and injects into the roles WordPress already has', function (): void {
    $GLOBALS['wp_roles'] = new WP_Roles;
    $GLOBALS['wp_roles']->roles = ['administrator' => ['name' => 'Administrator', 'capabilities' => []], 'author' => ['name' => 'Author', 'capabilities' => ['read' => true]], 'editor' => ['name' => 'Editor', 'capabilities' => []]];
    Brain\Monkey\Functions\when('did_action')->justReturn(0);
    ($this->discover)(EventManager::class, EditorAdjustments::class, EventCap::class, Event::class, Article::class, Genre::class);

    $this->discovery->apply();

    expect($GLOBALS['wp_roles']->roles)->toHaveKey('event_manager')
        ->and($GLOBALS['wp_roles']->roles['editor']['capabilities'])->toHaveKeys(['scan_tickets', 'edit_events'])
        ->and($GLOBALS['wp_roles']->roles['administrator']['capabilities'])->toHaveKeys(['export_attendees', 'edit_others_events', 'manage_genres']);
});

it('logs a declaration it cannot register and carries on', function (): void {
    $this->logger->shouldReceive('error')->once()->with(Mockery::pattern('/RedeclaresCoreRole: "editor" is a core role/'), Mockery::type('array'));
    ($this->discover)(RedeclaresCoreRole::class, EventManager::class);

    $this->discovery->apply();

    expect($this->registry->definitions())->toHaveCount(1);
});

it('identifies itself as roles', function (): void {
    expect($this->discovery->getIdentifier())->toBe('roles');
});
