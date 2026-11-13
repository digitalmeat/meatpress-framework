<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Blog;

/**
 * Event fired when a blog is restored from trash.
 *
 * This event is triggered when a blog is restored from the trash in a WordPress multisite network.
 * The blog becomes accessible again after restoration.
 */
class BlogRestored extends BlogEvent {}
