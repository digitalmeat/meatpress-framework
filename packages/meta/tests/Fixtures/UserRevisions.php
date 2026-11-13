<?php

declare(strict_types=1);

namespace Meatpress\Meta\Tests\Fixtures;

use Meatpress\Attributes\Meta;
use Meatpress\Attributes\UserMeta;

#[UserMeta]
class UserRevisions
{
    #[Meta(revisions: true)]
    public ?string $bio = null;
}
