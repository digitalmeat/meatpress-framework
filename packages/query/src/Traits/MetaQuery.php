<?php

declare(strict_types=1);

namespace Meatpress\Query\Traits;

use Meatpress\Query\QueryBuilder\SubQuery;
use Meatpress\Query\Traits\Query\SubQuery as SubQueryTrait;

trait MetaQuery
{
    /**
     * @var array<string|array>
     */
    protected ?array $metaQuery = null;

    use SubQueryTrait;

    public function metaQuery(callable|SubQuery $callback): self
    {
        $this->addSubQuery('meta', $callback);

        return $this;
    }
}
