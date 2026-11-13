<?php

declare(strict_types=1);

namespace Meatpress\Query\Traits;

use Meatpress\Query\QueryBuilder\SubQuery;
use Meatpress\Query\Traits\Query\SubQuery as SubQueryTrait;

trait TaxQuery
{
    /**
     * @var array<string|array>
     */
    protected ?array $taxQuery = null;

    use SubQueryTrait;

    public function taxQuery(callable|SubQuery $callback): self
    {
        $this->addSubQuery('tax', $callback);

        return $this;
    }
}
