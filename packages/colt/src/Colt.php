<?php

declare(strict_types=1);

namespace Meatpress\Colt;

use Illuminate\Contracts\Foundation\Application;

/**
 * Class Colt
 */
class Colt
{
    public static function isLaravel(): bool
    {
        return function_exists('app') && (
            app() instanceof Application ||
            str_starts_with(app()->version(), 'Lumen')
        );
    }
}
