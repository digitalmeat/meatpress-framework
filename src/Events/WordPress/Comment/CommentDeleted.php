<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Comment;

/**
 * Event fired when a comment is permanently deleted.
 *
 * This event is triggered when a comment is permanently removed from the database,
 * typically after being deleted from the trash.
 */
class CommentDeleted extends CommentEvent {}
