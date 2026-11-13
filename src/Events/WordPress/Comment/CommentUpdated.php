<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Comment;

/**
 * Event fired when a comment is updated.
 *
 * This event is triggered when a comment's content or metadata is modified.
 */
class CommentUpdated extends CommentEvent {}
