<?php

declare(strict_types=1);

namespace Tests\Unit\Meta\Fixtures;

use Meatpress\Attributes\Meta;
use Meatpress\Attributes\UserMeta;

#[UserMeta]
class MemberProfile
{
    #[Meta(showInRest: true)]
    public bool $newsletterOptIn = false;

    #[Meta]
    public ?string $jobTitle = null;
}
