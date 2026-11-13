<?php

declare(strict_types=1);

namespace Meatpress\Query\Traits\Sites;

use Meatpress\Query\Traits\Concerns\SkipsFoundRows;

trait Caching
{
    use SkipsFoundRows;

    protected ?bool $updateSiteCache = null;

    protected ?bool $updateSiteMetaCache = null;

    /**
     * Primes the cache of the found sites.
     */
    public function updateCache(bool $value = true): self
    {
        $this->updateSiteCache = $value;

        return $this;
    }

    public function updateMetaCache(bool $value = true): self
    {
        $this->updateSiteMetaCache = $value;

        return $this;
    }
}
