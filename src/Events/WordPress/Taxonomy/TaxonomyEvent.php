<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Taxonomy;

use WP_Term;

/**
 * Base class for all taxonomy-related events.
 *
 * This abstract class provides the foundation for all taxonomy events,
 * containing the term object that triggered the event.
 */
abstract class TaxonomyEvent
{
    /**
     * Constructor.
     */
    public function __construct(
        public readonly WP_Term $term
    ) {}
}
