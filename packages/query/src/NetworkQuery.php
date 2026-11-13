<?php

declare(strict_types=1);

namespace Meatpress\Query;

use Meatpress\Query\Traits\Concerns\BuildsArguments;
use Meatpress\Query\Traits\Concerns\LimitsResults;
use Meatpress\Query\Traits\Concerns\SelectsIds;
use Meatpress\Query\Traits\Networks\Caching;
use Meatpress\Query\Traits\Networks\Filters;
use Meatpress\Query\Traits\Networks\Order;
use Meatpress\Query\Traits\Networks\Retrieval;
use WP_Network_Query;

/**
 * Multisite.
 */
class NetworkQuery
{
    use BuildsArguments, Caching, Filters, LimitsResults, Order, Retrieval, SelectsIds;

    /**
     * @param  array<int>|int|null  $networkId  A network ID, or a list of network IDs.
     */
    public function __construct(array|int|null $networkId = null, ?string $fields = null)
    {
        if (is_int($networkId)) {
            $this->whereIn([$networkId]);
        } elseif (is_array($networkId)) {
            $this->whereIn($networkId);
        }

        if ($fields) {
            $this->fields($fields);
        }
    }

    /**
     * @param  array<int>|int|null  $networkId
     */
    public static function find(array|int|null $networkId = null): self
    {
        return new static($networkId);
    }

    public static function select(?string $fields = 'all'): self
    {
        return new static(null, $fields);
    }

    public function get(): WP_Network_Query
    {
        return new WP_Network_Query($this->getArguments());
    }
}
