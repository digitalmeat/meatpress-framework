<?php

declare(strict_types=1);

namespace Meatpress\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Facade for WordPress Taxonomy Query functionality.
 *
 * Provides a fluent interface for building WordPress taxonomy queries with
 * improved type safety and query validation.
 *
 * @mixin \Meatpress\Query\TaxQuery
 *
 * @see \Meatpress\Query\TaxQuery
 */
class TaxQuery extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'wp.query.taxonomy';
    }
}
