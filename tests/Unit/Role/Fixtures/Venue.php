<?php

declare(strict_types=1);

namespace Tests\Unit\Role\Fixtures;

use Meatpress\Attributes\PostType;
use Meatpress\Attributes\PostType\Capabilities;
use Meatpress\Attributes\PostType\CapabilityType;

#[PostType('venue')]
#[CapabilityType('venue')]
#[Capabilities(['publish_posts' => 'open_venues'])]
class Venue {}
