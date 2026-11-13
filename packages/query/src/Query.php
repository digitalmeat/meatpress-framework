<?php

declare(strict_types=1);

namespace Meatpress\Query;

use Meatpress\Query\QueryBuilder\DateQueryBuilder;
use Meatpress\Query\QueryBuilder\MetaQueryBuilder;
use Meatpress\Query\QueryBuilder\TaxQueryBuilder;

class Query
{
    public static function meta(string $key): MetaQueryBuilder
    {
        return new MetaQueryBuilder($key);
    }

    public static function date(string $key): DateQueryBuilder
    {
        return new DateQueryBuilder($key);
    }

    public static function tax(string $taxonomy): TaxQueryBuilder
    {
        return new TaxQueryBuilder($taxonomy);
    }
}
