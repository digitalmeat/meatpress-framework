<?php

declare(strict_types=1);

namespace Meatpress\Query\Traits\Networks;

use Meatpress\Query\Traits\Concerns\DisablesOrder;
use Meatpress\Query\Traits\Concerns\OrdersByMap;

trait Order
{
    use DisablesOrder, OrdersByMap;

    public function orderByDomain(string $order = 'ASC'): self
    {
        return $this->orderBy('domain', $order);
    }

    public function orderByPath(string $order = 'ASC'): self
    {
        return $this->orderBy('path', $order);
    }

    public function orderByDomainLength(string $order = 'ASC'): self
    {
        return $this->orderBy('domain_length', $order);
    }

    public function orderByPathLength(string $order = 'ASC'): self
    {
        return $this->orderBy('path_length', $order);
    }

    /**
     * Keeps the order of the IDs given to whereIn().
     */
    public function orderByInclude(): self
    {
        return $this->orderBy('network__in');
    }
}
