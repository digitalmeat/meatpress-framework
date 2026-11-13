<?php

declare(strict_types=1);

namespace Meatpress\Meta\Tests\Fixtures;

use Meatpress\Attributes\PostType;

#[PostType('no-meta')]
class NoMeta
{
    public string $title = '';
}
