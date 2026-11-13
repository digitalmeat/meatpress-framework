<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Plugins\UserSwitching;

use WP_User;

/**
 * Base class for all User Switching events.
 *
 * This abstract class provides the foundation for all User Switching events,
 * containing the user object that triggered the event.
 */
abstract class UserSwitchingEvent
{
    /**
     * Constructor.
     */
    public function __construct(
        public readonly WP_User $user
    ) {}
}
