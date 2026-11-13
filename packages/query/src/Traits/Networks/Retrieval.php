<?php

declare(strict_types=1);

namespace Meatpress\Query\Traits\Networks;

use Meatpress\Query\Traits\Concerns\RetrievesRecords;
use WP_Network_Query;

trait Retrieval
{
    use RetrievesRecords;

    protected function newWpQuery(): object
    {
        return new WP_Network_Query;
    }
}
