<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\User;

/**
 * Event fired when a user's password is reset.
 *
 * This event is triggered when a user successfully resets
 * their password through the password reset process.
 */
class UserPasswordReset extends UserEvent {}
