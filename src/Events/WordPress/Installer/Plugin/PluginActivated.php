<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Installer\Plugin;

/**
 * Event fired when a plugin is activated.
 *
 * This event is triggered when a plugin is activated in WordPress,
 * either on a single site or network-wide in a multisite setup.
 */
class PluginActivated extends PluginEvent {}
