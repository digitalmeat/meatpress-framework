<?php

declare(strict_types=1);

namespace Meatpress\Query;

use Meatpress\Query\Traits\Concerns\BuildsArguments;
use Meatpress\Query\Traits\Concerns\LimitsResults;
use Meatpress\Query\Traits\Concerns\SelectsIds;
use Meatpress\Query\Traits\DateQuery;
use Meatpress\Query\Traits\MetaField;
use Meatpress\Query\Traits\MetaQuery;
use Meatpress\Query\Traits\Sites\Caching;
use Meatpress\Query\Traits\Sites\Filters;
use Meatpress\Query\Traits\Sites\Order;
use Meatpress\Query\Traits\Sites\Retrieval;
use WP_Site_Query;

/**
 * Multisite. The `ID` argument of WP_Site_Query is `whereIn([$id])`.
 */
class SiteQuery
{
    use BuildsArguments, Caching, DateQuery, Filters, LimitsResults, MetaField, MetaQuery, Order, Retrieval, SelectsIds;

    /**
     * @param  array<int>|int|null  $siteId  A site ID, or a list of site IDs.
     */
    public function __construct(array|int|null $siteId = null, ?string $fields = null)
    {
        // WP_Site_Query returns at most 100 sites unless told otherwise: no cap is applied here (0 is "all").
        $this->number = 0;

        if (is_int($siteId)) {
            $this->whereIn([$siteId]);
        } elseif (is_array($siteId)) {
            $this->whereIn($siteId);
        }

        if ($fields) {
            $this->fields($fields);
        }
    }

    /**
     * @param  array<int>|int|null  $siteId
     */
    public static function find(array|int|null $siteId = null): self
    {
        return new static($siteId);
    }

    public static function select(?string $fields = 'all'): self
    {
        return new static(null, $fields);
    }

    public function get(): WP_Site_Query
    {
        return new WP_Site_Query($this->getArguments());
    }
}
