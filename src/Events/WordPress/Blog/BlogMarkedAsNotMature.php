<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Blog;

/**
 * Event fired when a blog is marked as not mature.
 *
 * This event is triggered when a blog's mature content flag is removed
 * in a WordPress multisite network. This typically restores normal display
 * and access settings for the blog.
 */
class BlogMarkedAsNotMature extends BlogEvent {}
