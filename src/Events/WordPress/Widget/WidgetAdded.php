<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Widget;

/**
 * Event fired when a widget is added to a sidebar.
 *
 * This event is triggered when a new widget is added to any sidebar,
 * including when widgets are moved from the inactive widgets area.
 */
class WidgetAdded extends WidgetEvent {}
