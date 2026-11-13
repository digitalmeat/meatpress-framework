<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Media;

/**
 * Event fired when a media attachment is deleted.
 *
 * This event is triggered when a media file is permanently removed
 * from the WordPress media library.
 */
class MediaDeleted extends MediaEvent {}
