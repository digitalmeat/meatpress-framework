<?php

declare(strict_types=1);

namespace Meatpress\Query\Traits\Sites;

use Meatpress\Query\Traits\Concerns\RetrievesRecords;
use WP_Site_Query;

trait Retrieval
{
    use RetrievesRecords;

    protected function newWpQuery(): object
    {
        return new WP_Site_Query;
    }
}
