<?php

declare(strict_types=1);

namespace Meatpress\Query\Traits\Networks;

use Meatpress\Query\Traits\Concerns\SkipsFoundRows;

trait Caching
{
    use SkipsFoundRows;

    protected ?bool $updateNetworkCache = null;

    /**
     * Primes the cache of the found networks.
     */
    public function updateCache(bool $value = true): self
    {
        $this->updateNetworkCache = $value;

        return $this;
    }
}
