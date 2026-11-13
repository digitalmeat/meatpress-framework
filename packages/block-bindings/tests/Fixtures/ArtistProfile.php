<?php

declare(strict_types=1);

namespace Meatpress\BlockBinding\Tests\Fixtures;

use Meatpress\Attributes\Meta;
use Meatpress\Attributes\UserMeta;

#[UserMeta]
class ArtistProfile
{
    #[Meta(public: true)]
    public ?string $stageName = null;

    #[Meta(showInRest: true)]
    public ?string $phone = null;
}
