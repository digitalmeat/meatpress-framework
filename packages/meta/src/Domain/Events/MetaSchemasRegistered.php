<?php

declare(strict_types=1);

namespace Meatpress\Meta\Domain\Events;

use Meatpress\Meta\Domain\Models\MetaSchema;

/**
 * Dispatched once the typed meta are registered with WordPress, on `init`.
 */
final readonly class MetaSchemasRegistered
{
    /**
     * @param  list<MetaSchema>  $schemas
     */
    public function __construct(public array $schemas) {}
}
