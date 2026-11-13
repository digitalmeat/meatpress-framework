<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\User;

/**
 * Event fired when a user logs out.
 *
 * This event is triggered when a user logs out of WordPress
 * or their session is terminated.
 */
class UserLoggedOut extends UserEvent {}
