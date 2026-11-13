<?php

declare(strict_types=1);

namespace Meatpress\BlockPattern\Infrastructure\Registrars;

use Meatpress\BlockPattern\Domain\Contracts\PatternCategoryRegistrarInterface;

/**
 * WordPress implementation of PatternCategoryRegistrarInterface.
 *
 * This is an adapter in hexagonal architecture that connects
 * our domain to WordPress for pattern category registration.
 */
class WordPressPatternCategoryRegistrar implements PatternCategoryRegistrarInterface
{
    /**
     * {@inheritdoc}
     */
    public function registerCategory(string $slug, array $attributes): void
    {
        if (! function_exists('register_block_pattern_category')) {
            return;
        }

        \register_block_pattern_category($slug, $attributes);
    }
}
