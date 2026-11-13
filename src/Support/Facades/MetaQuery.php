<?php

declare(strict_types=1);

namespace Meatpress\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Facade for WordPress Meta Query functionality.
 *
 * Provides a fluent interface for building WordPress meta queries with
 * improved type safety and query validation.
 *
 * @mixin \Meatpress\Query\MetaQuery
 *
 * @see \Meatpress\Query\MetaQuery
 */
class MetaQuery extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'wp.query.meta';
    }
}
