<?php

declare(strict_types=1);

namespace Meatpress\Asset\Infrastructure\Laravel;

use Meatpress\Asset\Domain\Contracts\PublicPath;

/**
 * The public directory of the Laravel application.
 */
final class LaravelPublicPath implements PublicPath
{
    public function path(string $to = ''): string
    {
        return public_path($to);
    }
}
