<?php

declare(strict_types=1);

namespace Meatpress\BlockBinding\Domain\Contracts;

use Meatpress\Colt\Model\Post;

/**
 * Loads the post of a block context as a model, for `BindingContext::post()`.
 * In a Meatpress application it is the framework's `Meatpress\Models\Post` (the
 * class bound to the post type, with its typed meta accessors); without it,
 * Colt's `Post`.
 */
interface PostFinderInterface
{
    public function find(int $postId): ?Post;
}
