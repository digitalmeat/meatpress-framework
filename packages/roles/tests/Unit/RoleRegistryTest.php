<?php

declare(strict_types=1);

use Meatpress\Role\Application\Services\CapabilityOwnerReader;
use Meatpress\Role\Application\Services\RoleDefinitionBuilder;
use Meatpress\Role\Application\Services\RoleRegistry;
use Meatpress\Role\Domain\Exceptions\InvalidRoleDefinitionException;
use Meatpress\Role\Domain\Models\RoleChanges;
use Meatpress\Role\Domain\Models\RoleDefinition;
use Meatpress\Role\Domain\Services\PostTypeCapabilityMap;
use Meatpress\Role\Tests\Fixtures\AlsoAdjustsEditor;
use Meatpress\Role\Tests\Fixtures\Article;
use Meatpress\Role\Tests\Fixtures\ContradictsEditorAdjustments;
use Meatpress\Role\Tests\Fixtures\DuplicateEventManager;
use Meatpress\Role\Tests\Fixtures\EditorAdjustments;
use Meatpress\Role\Tests\Fixtures\Event;
use Meatpress\Role\Tests\Fixtures\EventManager;
use Meatpress\Role\Tests\Fixtures\Genre;
use Meatpress\Role\Tests\Fixtures\LoopA;
use Meatpress\Role\Tests\Fixtures\LoopB;
use Meatpress\Role\Tests\Fixtures\Topic;
use Meatpress\Role\Tests\Fixtures\Usher;
use Meatpress\Role\Tests\Fixtures\Venue;

require_once dirname(__DIR__).'/Fixtures/Invalid.php';

beforeEach(function (): void {
    $this->registry = new RoleRegistry(new CapabilityOwnerReader, new PostTypeCapabilityMap);
    $this->build = fn (string $class): RoleDefinition|\Meatpress\Role\Domain\Models\RoleModification => (new RoleDefinitionBuilder(new CapabilityOwnerReader))->build($class);
});

it('resolves post type and taxonomy grants into capability names', function (): void {
    $this->registry->addPostType(Venue::class);
    $this->registry->addRole(($this->build)(EventManager::class));

    $grants = $this->registry->definitions()[0]->changes->grants;

    expect($grants)->toContain('export_attendees', 'upload_files', 'edit_others_events', 'delete_private_events', 'open_venues', 'edit_published_venues', 'manage_genres', 'assign_genres')
        ->not->toContain('edit_others_venues')
        ->and($grants)->toBe(array_values(array_unique($grants)));
});

it('ignores, with a warning, a post type slug it has not discovered', function (): void {
    $warnings = [];
    $this->registry->addRole(($this->build)(EventManager::class));

    $grants = $this->registry->definitions(function (string $warning) use (&$warnings): void {
        $warnings[] = $warning;
    })[0]->changes->grants;

    expect($grants)->not->toContain('open_venues')
        ->and($warnings)->toBe(['Meatpress\Role\Tests\Fixtures\EventManager: the post type "venue" was not found with its own capabilities; its grant is ignored.']);
});

it('ignores, with a warning, a taxonomy slug it has not discovered', function (): void {
    $warnings = [];
    $modification = ($this->build)(EditorAdjustments::class);
    $this->registry->addModification($modification->withChanges(new RoleChanges(taxonomyGrants: ['genre'])));

    $this->registry->modifications(function (string $warning) use (&$warnings): void {
        $warnings[] = $warning;
    });

    expect($warnings)->toBe(['Meatpress\Role\Tests\Fixtures\EditorAdjustments: the taxonomy "genre" was not found with its own capabilities; its grant is ignored.']);
});

it('collects every declared capability for the super roles', function (): void {
    $this->registry->addPostType(Event::class);
    $this->registry->addPostType(Article::class);
    $this->registry->addTaxonomy(Genre::class);
    $this->registry->addTaxonomy(Topic::class);
    $this->registry->addCapabilitySet(['export_attendees', 'scan_tickets']);
    $this->registry->addCapabilitySet(['scan_tickets']);

    expect($this->registry->declaredCapabilities())->toBe([
        'export_attendees', 'scan_tickets',
        'edit_events', 'delete_events', 'publish_events', 'edit_published_events', 'delete_published_events',
        'edit_others_events', 'delete_others_events', 'read_private_events', 'edit_private_events', 'delete_private_events',
        'manage_genres', 'edit_genres', 'assign_genres',
    ]);
});

it('starts empty', function (): void {
    expect($this->registry->isEmpty())->toBeTrue();

    $this->registry->addCapabilitySet(['scan_tickets']);

    expect($this->registry->isEmpty())->toBeFalse();
});

it('refuses a role slug declared by two classes', function (): void {
    $this->registry->addRole(($this->build)(EventManager::class));
    $this->registry->addRole(($this->build)(EventManager::class));

    $this->registry->addRole(($this->build)(DuplicateEventManager::class));
})->throws(InvalidRoleDefinitionException::class, 'the role "event_manager" is already declared by Meatpress\Role\Tests\Fixtures\EventManager');

it('refuses an inheritance loop', function (): void {
    $this->registry->addRole(($this->build)(LoopA::class));

    $this->registry->addRole(($this->build)(LoopB::class));
})->throws(InvalidRoleDefinitionException::class, 'inheritance loops: loop_b → loop_a → loop_b');

it('refuses modifications of the same role that contradict each other', function (): void {
    $this->registry->addModification(($this->build)(EditorAdjustments::class));
    $this->registry->addModification(($this->build)(AlsoAdjustsEditor::class));

    $this->registry->addModification(($this->build)(ContradictsEditorAdjustments::class));
})->throws(InvalidRoleDefinitionException::class, 'it contradicts Meatpress\Role\Tests\Fixtures\EditorAdjustments on the role "editor": "edit_theme_options" is both granted and removed.');

it('keeps one modification per class', function (): void {
    $this->registry->addModification(($this->build)(EditorAdjustments::class));
    $this->registry->addModification(($this->build)(EditorAdjustments::class));

    expect($this->registry->modifications())->toHaveCount(1);
});

it('lists the text domain of each label that declares one', function (): void {
    $this->registry->addRole(($this->build)(Usher::class));
    $this->registry->addRole(($this->build)(LoopB::class));

    expect($this->registry->labelDomains())->toBe(['Usher' => 'events']);
});
