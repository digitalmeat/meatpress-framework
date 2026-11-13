<?php

declare(strict_types=1);

namespace Meatpress\Role\Tests\Fixtures;

use Meatpress\Attributes\ModifyRole;
use Meatpress\Attributes\Role\Grants;
use Meatpress\Attributes\Role\GrantsPostType;
use Meatpress\Attributes\Role\Without;
use Meatpress\Role\Domain\Enums\Access;

#[ModifyRole('editor')]
#[GrantsPostType(Event::class, Access::Contributor)]
#[Grants(EventCap::ScanTickets)]
#[Without('edit_theme_options')]
final class EditorAdjustments {}
