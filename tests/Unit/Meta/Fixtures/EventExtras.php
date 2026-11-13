<?php

declare(strict_types=1);

namespace Tests\Unit\Meta\Fixtures;

use Meatpress\Attributes\Meta;
use Meatpress\Attributes\PostType;

#[PostType('event')]
class EventExtras
{
    #[Meta]
    public int $capacity = 0;
}
