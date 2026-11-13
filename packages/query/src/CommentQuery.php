<?php

declare(strict_types=1);

namespace Meatpress\Query;

use Meatpress\Query\Traits\Comments\Caching;
use Meatpress\Query\Traits\Comments\Filters;
use Meatpress\Query\Traits\Comments\Order;
use Meatpress\Query\Traits\Comments\PostFilters;
use Meatpress\Query\Traits\Comments\Retrieval;
use Meatpress\Query\Traits\Concerns\BuildsArguments;
use Meatpress\Query\Traits\Concerns\LimitsResults;
use Meatpress\Query\Traits\Concerns\PagedResults;
use Meatpress\Query\Traits\Concerns\SelectsIds;
use Meatpress\Query\Traits\DateQuery;
use Meatpress\Query\Traits\MetaField;
use Meatpress\Query\Traits\MetaQuery;
use WP_Comment_Query;

class CommentQuery
{
    use BuildsArguments, Caching, DateQuery, Filters, LimitsResults, MetaField, MetaQuery, Order, PagedResults, PostFilters, Retrieval, SelectsIds;

    /**
     * @param  array<int>|int|null  $commentId  A comment ID, or a list of comment IDs.
     */
    public function __construct(array|int|null $commentId = null, ?string $fields = null)
    {
        if (is_int($commentId)) {
            $this->whereIn([$commentId]);
        } elseif (is_array($commentId)) {
            $this->whereIn($commentId);
        }

        if ($fields) {
            $this->fields($fields);
        }
    }

    /**
     * @param  array<int>|int|null  $commentId
     */
    public static function find(array|int|null $commentId = null): self
    {
        return new static($commentId);
    }

    public static function select(?string $fields = 'all'): self
    {
        return new static(null, $fields);
    }

    public function get(): WP_Comment_Query
    {
        return new WP_Comment_Query($this->getArguments());
    }
}
