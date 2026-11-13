<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Installer\Theme;

/**
 * Event fired when a theme is installed.
 *
 * This event is triggered when a new theme is successfully installed
 * in WordPress, either through the admin interface or programmatically.
 */
class ThemeInstalled extends ThemeEvent {}
