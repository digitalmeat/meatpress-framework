<?php

declare(strict_types=1);

namespace Meatpress\Query;

use Meatpress\Query\Traits\Concerns\BuildsArguments;
use Meatpress\Query\Traits\Concerns\LimitsResults;
use Meatpress\Query\Traits\Concerns\PagedResults;
use Meatpress\Query\Traits\DateQuery;
use Meatpress\Query\Traits\MetaField;
use Meatpress\Query\Traits\MetaQuery;
use Meatpress\Query\Traits\User\Fields;
use Meatpress\Query\Traits\User\Filters;
use Meatpress\Query\Traits\User\Order;
use Meatpress\Query\Traits\User\Performance;
use Meatpress\Query\Traits\User\Retrieval;
use WP_User_Query;

class UserQuery
{
    use BuildsArguments, DateQuery, Fields, Filters, LimitsResults, MetaField, MetaQuery, Order, PagedResults, Performance, Retrieval;

    /**
     * @param  array<int>|int|null  $userId  A user ID, or a list of user IDs.
     * @param  string|array<string>|null  $fields
     */
    public function __construct(array|int|null $userId = null, string|array|null $fields = null)
    {
        if (is_int($userId)) {
            $this->whereIn([$userId]);
        } elseif (is_array($userId)) {
            $this->whereIn($userId);
        }

        if ($fields) {
            $this->fields($fields);
        }
    }

    /**
     * @param  array<int>|int|null  $userId
     */
    public static function find(array|int|null $userId = null): self
    {
        return new static($userId);
    }

    /**
     * @param  string|array<string>|null  $fields
     */
    public static function select(string|array|null $fields = 'all'): self
    {
        return new static(null, $fields);
    }

    public function get(): WP_User_Query
    {
        return new WP_User_Query($this->getArguments());
    }
}
