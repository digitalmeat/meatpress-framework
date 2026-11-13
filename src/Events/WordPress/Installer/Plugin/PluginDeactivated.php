<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Installer\Plugin;

/**
 * Event fired when a plugin is deactivated.
 *
 * This event is triggered when a plugin is deactivated in WordPress,
 * either on a single site or network-wide in a multisite setup.
 */
class PluginDeactivated extends PluginEvent {}
