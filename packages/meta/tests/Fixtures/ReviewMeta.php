<?php

declare(strict_types=1);

namespace Meatpress\Meta\Tests\Fixtures;

use Meatpress\Attributes\CommentMeta;
use Meatpress\Attributes\Meta;

#[CommentMeta]
class ReviewMeta
{
    #[Meta]
    public int $rating = 5;
}
