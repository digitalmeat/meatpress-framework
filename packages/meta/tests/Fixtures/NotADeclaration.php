<?php

declare(strict_types=1);

namespace Meatpress\Meta\Tests\Fixtures;

use Meatpress\Attributes\Meta;

class NotADeclaration
{
    #[Meta]
    public string $color = '';
}
