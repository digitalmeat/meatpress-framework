<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Plugins\GravityForms\Confirmation;

use Meatpress\Events\WordPress\Plugins\GravityForms\GravityFormsEvent;

/**
 * Base class for all Gravity Forms confirmation related events.
 *
 * This abstract class provides the foundation for all confirmation events,
 * containing the confirmation data and the associated form.
 */
abstract class ConfirmationEvent extends GravityFormsEvent
{
    /**
     * Constructor.
     *
     * @param  array  $confirmation  The confirmation data
     * @param  array  $form  The form data
     */
    public function __construct(
        public readonly array $confirmation,
        array $form
    ) {
        parent::__construct($form);
    }
}
