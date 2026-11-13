<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Menu;

/**
 * Event fired when a new navigation menu is created.
 *
 * This event is triggered when a new menu is created in the
 * WordPress navigation menu system.
 */
class MenuCreated extends MenuEvent {}
