<?php

declare(strict_types=1);

namespace Tests\Unit\Role\Fixtures;

use Meatpress\Attributes\PostType;
use Meatpress\Attributes\PostType\CapabilityType;
use Meatpress\Attributes\PostType\MapMetaCap;

#[PostType('event')]
#[CapabilityType('event')]
#[MapMetaCap]
class Event {}
