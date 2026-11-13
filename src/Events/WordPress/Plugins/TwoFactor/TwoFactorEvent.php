<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Plugins\TwoFactor;

use WP_User;

/**
 * Base class for all Two Factor authentication related events.
 *
 * This abstract class provides the foundation for all Two Factor events,
 * containing the user object that triggered the event.
 */
abstract class TwoFactorEvent
{
    /**
     * Constructor.
     */
    public function __construct(
        public readonly WP_User $user
    ) {}
}
