<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Taxonomy;

/**
 * Event fired when a term is deleted.
 *
 * This event is triggered when a term is permanently removed from any taxonomy,
 * except for excluded taxonomies like nav_menu.
 */
class TermDeleted extends TaxonomyEvent {}
