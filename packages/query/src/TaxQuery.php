<?php

declare(strict_types=1);

namespace Meatpress\Query;

use Meatpress\Query\QueryBuilder\QueryBuilder;
use Meatpress\Query\QueryBuilder\TaxQueryBuilder;

class TaxQuery extends QueryBuilder
{
    public function taxonomy(string $taxonomy): TaxQueryBuilder
    {
        return new TaxQueryBuilder($taxonomy);
    }
}
