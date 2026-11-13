<?php

declare(strict_types=1);

namespace Meatpress\Hook\Tests\Fixtures\Async;

use Meatpress\Hook\Async\AsyncContext;

final class InjectedHandler
{
    /** @var list<array<string, mixed>> */
    public static array $calls = [];

    public function send(int $postId, Mailer $mailer, AsyncContext $context, ?\WP_Post $post = null): void
    {
        self::$calls[] = ['postId' => $postId, 'transport' => $mailer->transport, 'hook' => $context->hook, 'post' => $post?->ID];
    }
}
