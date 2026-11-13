<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Post;

/**
 * Event fired when a post is permanently deleted.
 *
 * This event is triggered when a post is permanently removed from the database,
 * typically after being deleted from the trash.
 */
class PostDeleted extends PostEvent {}
