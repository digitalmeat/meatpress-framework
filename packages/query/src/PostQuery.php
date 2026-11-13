<?php

declare(strict_types=1);

namespace Meatpress\Query;

use Meatpress\Query\Traits\Author;
use Meatpress\Query\Traits\Caching;
use Meatpress\Query\Traits\Category;
use Meatpress\Query\Traits\Comment;
use Meatpress\Query\Traits\Concerns\BuildsArguments;
use Meatpress\Query\Traits\DateQuery;
use Meatpress\Query\Traits\Field;
use Meatpress\Query\Traits\MetaField;
use Meatpress\Query\Traits\MetaQuery;
use Meatpress\Query\Traits\MimeType;
use Meatpress\Query\Traits\Order;
use Meatpress\Query\Traits\Pagination;
use Meatpress\Query\Traits\Password;
use Meatpress\Query\Traits\Period;
use Meatpress\Query\Traits\Permission;
use Meatpress\Query\Traits\Post as PostTrait;
use Meatpress\Query\Traits\PostType;
use Meatpress\Query\Traits\Retrieval;
use Meatpress\Query\Traits\Search;
use Meatpress\Query\Traits\Status;
use Meatpress\Query\Traits\Tag;
use Meatpress\Query\Traits\TaxQuery;
use WP_Query;

class PostQuery
{
    use Author, BuildsArguments, Caching, Category, Comment, DateQuery, Field, MetaField, MetaQuery, MimeType, Order, Pagination, Password, Period, Permission, PostTrait, PostType, Retrieval, Search, Status, Tag, TaxQuery;

    public function __construct(array|int|null $postId = null, ?string $fields = null)
    {
        if (is_int($postId)) {
            $this->postId($postId);
        } elseif (is_array($postId)) {
            $this->whereIn($postId);
        }

        if ($fields) {
            $this->fields($fields);
        }
    }

    public static function find(array|int|null $postId = null): self
    {
        return new static($postId);
    }

    public static function select(?string $fields = 'all'): self
    {
        return new static(null, $fields);
    }

    public function get(): WP_Query
    {
        $args = $this->getArguments();

        return new WP_Query($args);
    }
}
