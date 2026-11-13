<?php

declare(strict_types=1);

namespace Meatpress\Conventions\Hooks;

/**
 * Stores ACF field groups as JSON in the active theme, so they are versioned
 * with the code and synced between environments.
 *
 * @see https://www.advancedcustomfields.com/resources/local-json/
 */
final class AcfLocalJson
{
    public function __construct(private readonly string $directory) {}

    public function savePath(): string
    {
        return $this->path();
    }

    /**
     * @param  list<string>  $paths
     * @return list<string>
     */
    public function loadPaths(array $paths): array
    {
        return [$this->path()];
    }

    private function path(): string
    {
        return get_stylesheet_directory().'/'.trim($this->directory, '/');
    }
}
