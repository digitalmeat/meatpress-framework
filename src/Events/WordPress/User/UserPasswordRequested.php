<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\User;

/**
 * Event fired when a user requests a password reset.
 *
 * This event is triggered when a user initiates the "Lost Password"
 * process by requesting a password reset link.
 */
class UserPasswordRequested extends UserEvent {}
