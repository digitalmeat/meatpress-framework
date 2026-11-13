<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Comment;

/**
 * Base class for all comment-related events.
 *
 * This abstract class provides the foundation for all comment events,
 * containing the comment object that triggered the event.
 */
abstract class CommentEvent
{
    /**
     * Constructor.
     */
    public function __construct(
        public readonly \WP_Comment $comment
    ) {}
}
