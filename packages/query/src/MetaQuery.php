<?php

declare(strict_types=1);

namespace Meatpress\Query;

use Meatpress\Query\QueryBuilder\MetaQueryBuilder;
use Meatpress\Query\QueryBuilder\QueryBuilder;

class MetaQuery extends QueryBuilder
{
    public function meta(string $key): MetaQueryBuilder
    {
        return new MetaQueryBuilder($key);
    }
}
