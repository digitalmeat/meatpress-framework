<?php

declare(strict_types=1);

namespace Meatpress\Role\Tests\Fixtures;

use Meatpress\Attributes\Role;
use Meatpress\Attributes\Role\Grants;
use Meatpress\Attributes\Role\GrantsPostType;
use Meatpress\Attributes\Role\GrantsTaxonomy;
use Meatpress\Attributes\Role\Without;
use Meatpress\Role\Domain\Enums\Access;

#[Role('event_manager', label: 'Event manager', inherits: 'author')]
#[GrantsPostType(Event::class, Access::Editor)]
#[GrantsPostType('venue', Access::Author)]
#[GrantsTaxonomy(Genre::class)]
#[Grants(EventCap::ExportAttendees, 'scan_tickets')]
#[Grants('upload_files')]
#[Without('publish_posts')]
final class EventManager {}
