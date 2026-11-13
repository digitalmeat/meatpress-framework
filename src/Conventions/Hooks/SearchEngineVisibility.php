<?php

declare(strict_types=1);

namespace Meatpress\Conventions\Hooks;

/**
 * Keeps every environment but production out of search engines.
 *
 * Meatpress derives the WordPress environment type from APP_ENV, so a staging
 * or local copy of a site is never indexed.
 */
final class SearchEngineVisibility
{
    /**
     * @param  array<string, bool|string>  $robots
     * @return array<string, bool|string>
     */
    public function filterRobots(array $robots): array
    {
        if (wp_get_environment_type() === 'production') {
            return $robots;
        }

        $robots['noindex'] = true;
        $robots['nofollow'] = true;

        return $robots;
    }
}
