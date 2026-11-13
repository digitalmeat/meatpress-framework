<?php

declare(strict_types=1);

namespace Meatpress\Meta\Domain\Contracts;

use Meatpress\Meta\Domain\Exceptions\MetaValidationException;
use Meatpress\Meta\Domain\Models\MetaDefinition;

/**
 * Checks a value against the `rules` of its meta.
 */
interface MetaValidatorInterface
{
    /**
     * @param  mixed  $value  The value with its PHP type
     *
     * @throws MetaValidationException When a rule fails
     */
    public function validate(MetaDefinition $definition, mixed $value): void;
}
