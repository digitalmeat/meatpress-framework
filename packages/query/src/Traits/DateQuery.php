<?php

declare(strict_types=1);

namespace Meatpress\Query\Traits;

use Meatpress\Query\QueryBuilder\SubQuery;
use Meatpress\Query\Traits\Query\SubQuery as SubQueryTrait;

trait DateQuery
{
    /**
     * @var array<string|array>
     */
    protected ?array $dateQuery = null;

    use SubQueryTrait;

    public function dateQuery(callable|SubQuery $callback): self
    {
        $this->addSubQuery('date', $callback);

        return $this;
    }
}
