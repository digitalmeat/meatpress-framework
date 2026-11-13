<?php

declare(strict_types=1);

namespace Meatpress\Meta\Tests\Fixtures;

use Meatpress\Attributes\Meta;
use Meatpress\Attributes\PostMeta;

#[PostMeta(['post', 'page'])]
class ArticleExtras
{
    #[Meta(showInRest: true, revisions: true)]
    public ?string $subtitle = null;
}
