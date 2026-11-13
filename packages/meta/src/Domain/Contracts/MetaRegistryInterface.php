<?php

declare(strict_types=1);

namespace Meatpress\Meta\Domain\Contracts;

use Meatpress\Meta\Domain\Models\MetaSchema;

/**
 * Registers typed meta with WordPress (`register_meta()`).
 */
interface MetaRegistryInterface
{
    public function register(MetaSchema $schema): void;
}
