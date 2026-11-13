<?php

declare(strict_types=1);

namespace Meatpress\BlockBinding\Tests\Fixtures;

use Meatpress\Attributes\Meta;
use Meatpress\Attributes\TermMeta;

#[TermMeta('venue')]
class VenueExtras
{
    #[Meta(showInRest: true)]
    public ?string $city = null;
}
