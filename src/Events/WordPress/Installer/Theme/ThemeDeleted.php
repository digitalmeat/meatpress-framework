<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Installer\Theme;

/**
 * Event fired when a theme is deleted.
 *
 * This event is triggered when a theme is permanently deleted
 * from the WordPress installation.
 */
class ThemeDeleted extends ThemeEvent {}
