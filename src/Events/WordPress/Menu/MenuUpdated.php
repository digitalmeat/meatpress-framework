<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Menu;

/**
 * Event fired when a navigation menu is updated.
 *
 * This event is triggered when an existing menu's items or settings
 * are modified in the WordPress navigation menu system.
 */
class MenuUpdated extends MenuEvent {}
