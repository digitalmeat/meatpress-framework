<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Post;

/**
 * Event fired when a post is restored from trash.
 *
 * This event is triggered when a post transitions from 'trash' status
 * to any other status.
 */
class PostRestored extends PostEvent {}
