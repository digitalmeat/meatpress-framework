<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Comment;

/**
 * Event fired when a comment is moved to trash.
 *
 * This event is triggered when a comment is moved to the trash bin.
 * The comment can still be restored or permanently deleted.
 */
class CommentTrashed extends CommentEvent {}
