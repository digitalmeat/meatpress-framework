<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Comment;

/**
 * Event fired when a comment is restored from trash.
 *
 * This event is triggered when a comment is restored from the trash bin
 * back to its previous status.
 */
class CommentRestored extends CommentEvent {}
