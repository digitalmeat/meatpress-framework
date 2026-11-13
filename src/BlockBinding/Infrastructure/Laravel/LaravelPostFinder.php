<?php

declare(strict_types=1);

namespace Meatpress\BlockBinding\Infrastructure\Laravel;

use Meatpress\BlockBinding\Domain\Contracts\PostFinderInterface;
use Meatpress\Models\Post;

/**
 * Loads the post as the framework's Post model, the class bound to its post type.
 */
final class LaravelPostFinder implements PostFinderInterface
{
    public function find(int $postId): ?Post
    {
        /** @var Post|null $post */
        $post = Post::query()->find($postId);

        return $post;
    }
}
