<?php

declare(strict_types=1);

namespace Meatpress\Theme\Infrastructure\Services;

use stdClass;

/**
 * Keeps wordpress.org from offering updates for themes it does not distribute.
 *
 * Meatpress themes are scaffolded from a starter repository into the project's
 * themes directory, then edited. WordPress still sends every installed theme to
 * the wordpress.org update API keyed by its directory name, so a theme whose
 * name happens to match a published slug — `default` does, and it is what
 * meatpress:install generates — is offered an update from a completely unrelated
 * theme. Accepting it replaces the project's theme with that download.
 *
 * The `Update URI` header is not enough on its own: WordPress consults the
 * matching `update_themes_{$hostname}` filter only when wordpress.org returned
 * nothing for that theme, which is exactly not the case when the name collides.
 */
final readonly class ThemeUpdateGuard
{
    public function __construct(private string $themesPath) {}

    /**
     * Drop wordpress.org update offers aimed at a theme Meatpress scaffolded.
     *
     * @param  mixed  $transient  The update_themes site transient
     */
    public function filterUpdates(mixed $transient): mixed
    {
        if (! $transient instanceof stdClass || ! isset($transient->response) || ! is_array($transient->response)) {
            return $transient;
        }

        foreach (array_keys($transient->response) as $stylesheet) {
            if (! is_string($stylesheet) || ! $this->isMeatpressTheme($stylesheet)) {
                continue;
            }

            $offer = $transient->response[$stylesheet];
            unset($transient->response[$stylesheet]);

            // Moved rather than dropped, so WordPress considers the theme
            // checked and does not keep asking on every admin page load.
            if (isset($transient->no_update) && is_array($transient->no_update)) {
                $transient->no_update[$stylesheet] = $offer;
            }
        }

        return $transient;
    }

    /**
     * A theme living in the project's themes directory is one of ours.
     */
    private function isMeatpressTheme(string $stylesheet): bool
    {
        if ($stylesheet === '' || str_contains($stylesheet, '..')) {
            return false;
        }

        return is_dir($this->themesPath.DIRECTORY_SEPARATOR.$stylesheet);
    }
}
