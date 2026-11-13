<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\User;

/**
 * Event fired when a user logs in.
 *
 * This event is triggered when a user successfully authenticates
 * and logs into WordPress.
 */
class UserLoggedIn extends UserEvent {}
