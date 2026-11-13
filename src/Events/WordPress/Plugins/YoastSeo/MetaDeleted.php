<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Plugins\YoastSeo;

/**
 * Event fired when a Yoast SEO meta is deleted.
 *
 * This event is triggered when a Yoast SEO meta field is removed from a post.
 */
class MetaDeleted extends MetaEvent {}
