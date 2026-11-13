<?php

declare(strict_types=1);

namespace Meatpress\BlockBinding\Tests\Fixtures;

use Meatpress\Attributes\BlockBinding;
use Meatpress\BlockBinding\Domain\Models\BindingContext;

#[BlockBinding('acme/echo', usesContext: [])]
final class InvokableBinding
{
    public function __invoke(BindingContext $context): ?string
    {
        return is_string($context->arg('say')) ? $context->arg('say') : null;
    }
}
