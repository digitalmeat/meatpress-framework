<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Plugins\GravityForms\Notification;

/**
 * Event fired when a Gravity Forms notification is deleted.
 *
 * This event is triggered when a notification is removed from a form
 * in the Gravity Forms system.
 */
class NotificationDeleted extends NotificationEvent {}
