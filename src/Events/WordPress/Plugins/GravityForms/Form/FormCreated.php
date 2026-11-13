<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Plugins\GravityForms\Form;

use Meatpress\Events\WordPress\Plugins\GravityForms\GravityFormsEvent;

/**
 * Event fired when a new Gravity Forms form is created.
 *
 * This event is triggered when a new form is created in the Gravity Forms system.
 */
class FormCreated extends GravityFormsEvent {}
