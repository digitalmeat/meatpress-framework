<?php

declare(strict_types=1);

namespace Meatpress\Colt\Model;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class Page
 */
class Page extends Post
{
    /**
     * @var string
     */
    protected $postType = 'page';

    /**
     * @return mixed
     */
    public function scopeHome(Builder $query)
    {
        return $query
            ->where('ID', '=', Option::get('page_on_front'))
            ->limit(1);
    }
}
