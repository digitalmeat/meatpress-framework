<?php

declare(strict_types=1);

namespace Meatpress\Role\Tests\Fixtures;

use Meatpress\Attributes\Role;
use Meatpress\Attributes\Role\Grants;

#[Role('steward', inherits: EventManager::class, allowSensitive: true)]
#[Grants('manage_options')]
final class Steward {}
