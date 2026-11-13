<?php

declare(strict_types=1);

namespace Meatpress\BlockBinding\Tests\Fixtures;

final class SeatCounter
{
    public function left(int $postId): int
    {
        return $postId * 2;
    }
}
