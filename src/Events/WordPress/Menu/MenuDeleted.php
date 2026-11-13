<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Menu;

/**
 * Event fired when a navigation menu is deleted.
 *
 * This event is triggered when a menu is permanently removed
 * from the WordPress navigation menu system.
 */
class MenuDeleted extends MenuEvent {}
