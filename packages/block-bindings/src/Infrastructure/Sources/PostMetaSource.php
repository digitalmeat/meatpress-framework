<?php

declare(strict_types=1);

namespace Meatpress\BlockBinding\Infrastructure\Sources;

use Meatpress\Attributes\BlockBinding;
use Meatpress\BlockBinding\Domain\Models\BindingContext;
use Meatpress\BlockBinding\Infrastructure\Services\BindingFormatter;
use Meatpress\Meta\Domain\Enums\MetaObjectType;

/**
 * `meatpress/post-meta`: a typed meta of the post of the block, formatted by its
 * type. `"args": {"key": "starts_at", "format": "l j F Y"}`.
 *
 * Reads only the meta a `#[Meta]` declares with `showInRest: true` under a key
 * that is not protected, as `core/post-meta` does.
 */
#[BlockBinding('meatpress/post-meta', label: 'Post meta (Meatpress)')]
final readonly class PostMetaSource
{
    public function __construct(
        private TypedMetaReader $reader,
        private BindingFormatter $formatter,
    ) {}

    public function __invoke(BindingContext $context): string|int|float|bool|null
    {
        $key = $context->arg('key');

        if (! is_string($key) || $key === '' || $context->postId === null) {
            return null;
        }

        $postType = $context->postType ?? (\get_post_type($context->postId) ?: null);
        $meta = $this->reader->read(MetaObjectType::Post, $postType, $context->postId, $key, TypedMetaReader::exposedInRest(...));

        return $meta === null ? null : $this->formatter->format($meta[0], $meta[1], $context->args, $context->attribute);
    }
}
