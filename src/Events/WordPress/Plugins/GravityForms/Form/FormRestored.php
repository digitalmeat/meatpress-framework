<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Plugins\GravityForms\Form;

use Meatpress\Events\WordPress\Plugins\GravityForms\GravityFormsEvent;

/**
 * Event fired when a Gravity Forms form is restored from trash.
 *
 * This event is triggered when a form is restored from the trash in the Gravity Forms system.
 */
class FormRestored extends GravityFormsEvent {}
