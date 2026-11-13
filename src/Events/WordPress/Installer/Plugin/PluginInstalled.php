<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Installer\Plugin;

/**
 * Event fired when a plugin is installed.
 *
 * This event is triggered when a new plugin is successfully installed
 * in WordPress, either through the admin interface or programmatically.
 */
class PluginInstalled extends PluginEvent {}
