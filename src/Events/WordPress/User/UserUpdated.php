<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\User;

/**
 * Event fired when a user's profile is updated.
 *
 * This event is triggered when a user's profile information
 * is modified in WordPress.
 */
class UserUpdated extends UserEvent {}
