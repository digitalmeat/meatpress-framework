<?php

declare(strict_types=1);

namespace Meatpress\Role\Tests\Fixtures;

use Meatpress\Attributes\PostType;
use Meatpress\Attributes\PostType\CapabilityType;
use Meatpress\Attributes\PostType\MapMetaCap;

#[PostType('event')]
#[CapabilityType('event')]
#[MapMetaCap]
class Event {}
