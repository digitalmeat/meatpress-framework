<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Comment;

/**
 * Event fired when a new comment is created.
 *
 * This event is triggered when a new comment is added to a post,
 * whether it's pending approval or automatically approved.
 */
class CommentCreated extends CommentEvent {}
