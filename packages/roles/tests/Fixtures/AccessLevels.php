<?php

declare(strict_types=1);

namespace Meatpress\Role\Tests\Fixtures;

use Meatpress\Attributes\Role;
use Meatpress\Attributes\Role\GrantsPostType;
use Meatpress\Role\Domain\Enums\Access;

#[Role('event_contributor', label: 'Event contributor')]
#[GrantsPostType(Event::class, Access::Contributor)]
final class ContributesEvents {}

#[Role('event_author', label: 'Event author')]
#[GrantsPostType(Event::class, Access::Author)]
final class AuthorsEvents {}

#[Role('event_editor', label: 'Event editor')]
#[GrantsPostType(Event::class)]
final class EditsEvents {}
