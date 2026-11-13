<?php

declare(strict_types=1);

namespace Meatpress\Conventions\Console;

use Illuminate\Console\Command;

/**
 * Activates the WordPress plugins installed by Composer.
 *
 * Composer only puts the plugin files in place; WordPress keeps the list of
 * active plugins in the database. Run this after a fresh install, or after
 * requiring a new plugin.
 *
 * Plugins listed in meatpress.optional_plugins (GTM4WP, GA Google Analytics)
 * are only activated when named explicitly.
 */
final class ActivatePluginsCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'plugins:activate
                            {plugins?* : Plugin directory names; every installed plugin but the optional ones when omitted}
                            {--except=* : Plugin directory names to leave inactive}';

    /**
     * @var string
     */
    protected $description = 'Activate the WordPress plugins installed by Composer';

    public function handle(): int
    {
        if (! function_exists('get_plugins')) {
            require_once ABSPATH.'wp-admin/includes/plugin.php';
        }

        /** @var list<string> $requested */
        $requested = $this->argument('plugins');
        /** @var list<string> $excluded */
        $excluded = $this->option('except');
        /** @var list<string> $optional */
        $optional = config('meatpress.optional_plugins', []);

        $failed = false;

        foreach (array_keys(get_plugins()) as $file) {
            $slug = dirname($file);

            if ($requested !== [] && ! in_array($slug, $requested, true)) {
                continue;
            }

            if (in_array($slug, $excluded, true)) {
                $this->components->twoColumnDetail($slug, '<fg=yellow>skipped</>');

                continue;
            }

            if ($requested === [] && in_array($slug, $optional, true)) {
                $this->components->twoColumnDetail($slug, '<fg=yellow>optional, not activated</>');

                continue;
            }

            if (is_plugin_active($file)) {
                $this->components->twoColumnDetail($slug, 'already active');

                continue;
            }

            $result = activate_plugin($file);

            if (is_wp_error($result)) {
                $failed = true;
                $this->components->twoColumnDetail($slug, '<fg=red>'.$result->get_error_message().'</>');

                continue;
            }

            $this->components->twoColumnDetail($slug, '<fg=green>activated</>');
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
