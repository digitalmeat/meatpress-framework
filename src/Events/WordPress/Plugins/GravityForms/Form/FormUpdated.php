<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Plugins\GravityForms\Form;

use Meatpress\Events\WordPress\Plugins\GravityForms\GravityFormsEvent;

/**
 * Event fired when a Gravity Forms form is updated.
 *
 * This event is triggered when an existing form is modified in the Gravity Forms system.
 */
class FormUpdated extends GravityFormsEvent {}
