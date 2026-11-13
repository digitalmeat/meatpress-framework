<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Plugins\GravityForms\Form;

use Meatpress\Events\WordPress\Plugins\GravityForms\GravityFormsEvent;

/**
 * Event fired when a Gravity Forms form is permanently deleted.
 *
 * This event is triggered when a form is permanently removed from the Gravity Forms system.
 */
class FormDeleted extends GravityFormsEvent {}
