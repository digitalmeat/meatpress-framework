<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Plugins\GravityForms\Form;

use Meatpress\Events\WordPress\Plugins\GravityForms\GravityFormsEvent;

/**
 * Event fired when a Gravity Forms form is moved to trash.
 *
 * This event is triggered when a form is moved to the trash in the Gravity Forms system.
 */
class FormTrashed extends GravityFormsEvent {}
