<?php

declare(strict_types=1);

namespace Meatpress\Attributes\Contracts;

use Meatpress\Attributes\Attributable;
use ReflectionClass;
use ReflectionMethod;

interface HandlesAttributes
{
    public function handle($container, Attributable $instance, ReflectionClass|ReflectionMethod $context, object $attribute): void;
}
