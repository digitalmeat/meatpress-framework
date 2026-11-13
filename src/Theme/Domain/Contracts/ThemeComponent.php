<?php

declare(strict_types=1);

namespace Meatpress\Theme\Domain\Contracts;

/**
 * Interface for theme components that can be registered
 */
interface ThemeComponent
{
    /**
     * Register the component
     */
    public function register(): void;
}
