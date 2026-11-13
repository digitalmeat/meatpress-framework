<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Post;

/**
 * Event fired when a post is moved to trash.
 *
 * This event is triggered when a post transitions to the 'trash' status.
 */
class PostTrashed extends PostEvent {}
