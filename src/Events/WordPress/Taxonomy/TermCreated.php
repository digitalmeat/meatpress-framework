<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Taxonomy;

/**
 * Event fired when a new term is created.
 *
 * This event is triggered when a new term is added to any taxonomy,
 * except for excluded taxonomies like nav_menu.
 */
class TermCreated extends TaxonomyEvent {}
