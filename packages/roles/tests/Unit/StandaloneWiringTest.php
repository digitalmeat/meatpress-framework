<?php

declare(strict_types=1);

use Meatpress\Discovery\Domain\Models\DiscoveryLocation;
use Meatpress\Role\Application\Services\CapabilityOwnerReader;
use Meatpress\Role\Application\Services\RoleDefinitionBuilder;
use Meatpress\Role\Application\Services\RoleRegistry;
use Meatpress\Role\Domain\Services\PostTypeCapabilityMap;
use Meatpress\Role\Domain\Services\RoleCompiler;
use Meatpress\Role\Infrastructure\Adapters\WordPressRoleInjector;
use Meatpress\Role\Infrastructure\Services\RoleDiscovery;
use Meatpress\Role\Tests\Fixtures\EditorAdjustments;
use Meatpress\Role\Tests\Fixtures\Event;
use Meatpress\Role\Tests\Fixtures\EventManager;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;

require_once dirname(__DIR__).'/Fixtures/wordpress-roles.php';

/**
 * The wiring the README shows for a project without Laravel: the pieces the framework's provider builds, built by
 * hand, from the attributes to the roles WordPress holds once `wp_roles_init` runs the injector.
 */
it('declares roles and hands them to WordPress with nothing but its own classes', function (): void {
    $reader = new CapabilityOwnerReader;
    $registry = new RoleRegistry($reader, new PostTypeCapabilityMap);
    $injector = new WordPressRoleInjector($registry, new RoleCompiler, ['administrator']);
    $discovery = new RoleDiscovery(new RoleDefinitionBuilder($reader, ['administrator']), $registry, $injector);

    $location = new DiscoveryLocation('Meatpress\\Role\\Tests\\', __DIR__);
    foreach ([Event::class, EventManager::class, EditorAdjustments::class] as $class) {
        $discovery->discover($location, DiscoveredClass::fromReflection(new ReflectionClass($class)));
    }
    $discovery->apply();

    $wpRoles = new WP_Roles;
    $wpRoles->roles = [
        'administrator' => ['name' => 'Administrator', 'capabilities' => ['manage_options' => true]],
        'editor' => ['name' => 'Editor', 'capabilities' => ['edit_posts' => true, 'edit_theme_options' => true]],
        'author' => ['name' => 'Author', 'capabilities' => ['read' => true, 'edit_posts' => true, 'publish_posts' => true, 'upload_files' => true]],
    ];

    $injector->inject($wpRoles);

    expect($wpRoles->roles)->toHaveKey('event_manager')
        ->and($wpRoles->role_names['event_manager'])->toBe('Event manager')
        ->and($wpRoles->roles['event_manager']['capabilities'])->toHaveKeys(['read', 'export_attendees', 'edit_others_events'])
        ->and($wpRoles->roles['event_manager']['capabilities'])->not->toHaveKey('publish_posts')
        ->and($wpRoles->roles['editor']['capabilities'])->not->toHaveKey('edit_theme_options')
        ->and($wpRoles->roles['administrator']['capabilities'])->toHaveKey('edit_others_events');
});
