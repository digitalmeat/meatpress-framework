<?php

declare(strict_types=1);

namespace Meatpress\Hook\Tests\Fixtures\Async;

enum OrderStatus: string
{
    case Completed = 'completed';
    case Refunded = 'refunded';
}
