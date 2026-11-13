<?php

declare(strict_types=1);

namespace Meatpress\Meta\Tests\Fixtures;

use Meatpress\Attributes\Meta;
use Meatpress\Attributes\Taxonomy;

#[Taxonomy]
class BookGenre
{
    #[Meta(showInRest: true)]
    public ?string $color = null;
}
