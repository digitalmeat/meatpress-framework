<?php

declare(strict_types=1);

namespace Meatpress\Meta\Tests\Fixtures;

use Meatpress\Attributes\Meta;
use Meatpress\Attributes\PostMeta;

#[PostMeta('page')]
class PageExtras
{
    #[Meta]
    public ?string $subtitle = null;
}
