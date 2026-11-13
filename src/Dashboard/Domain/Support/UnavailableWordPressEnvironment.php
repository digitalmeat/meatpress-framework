<?php

declare(strict_types=1);

namespace Meatpress\Dashboard\Domain\Support;

use Meatpress\Dashboard\Domain\Contracts\WordPressEnvironmentInterface;

/**
 * The environment of a collector that runs without WordPress: nothing is known.
 */
final readonly class UnavailableWordPressEnvironment implements WordPressEnvironmentInterface
{
    public function locale(): ?string
    {
        return null;
    }

    public function version(): ?string
    {
        return null;
    }

    public function isMultisite(): bool
    {
        return false;
    }

    public function permalinkStructure(): string
    {
        return '';
    }

    public function postTypeLabel(string $slug): ?string
    {
        return null;
    }

    public function taxonomyLabel(string $slug): ?string
    {
        return null;
    }

    public function activeTheme(): ?array
    {
        return null;
    }
}
