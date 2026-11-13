<?php

declare(strict_types=1);

namespace Meatpress\Query\Traits\User;

use Meatpress\Query\Traits\Concerns\CachesResults;

trait Performance
{
    use CachesResults;

    protected ?bool $countTotal = null;

    /**
     * Whether the total number of users is counted. Disable it when pagination is not needed.
     */
    public function countTotal(bool $value = true): self
    {
        $this->countTotal = $value;

        return $this;
    }

    public function withoutCount(): self
    {
        return $this->countTotal(false);
    }
}
