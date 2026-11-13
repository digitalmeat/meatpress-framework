<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Media;

/**
 * Event fired when a media attachment is updated.
 *
 * This event is triggered when an existing media file's metadata or details
 * are modified in the WordPress media library.
 */
class MediaUpdated extends MediaEvent {}
