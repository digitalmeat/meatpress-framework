<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Plugins\GravityForms\Confirmation;

/**
 * Event fired when a Gravity Forms confirmation is deleted.
 *
 * This event is triggered when a confirmation is removed from a form
 * in the Gravity Forms system.
 */
class ConfirmationDeleted extends ConfirmationEvent {}
