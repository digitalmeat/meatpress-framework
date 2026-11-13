<?php

declare(strict_types=1);

namespace Meatpress\Conventions\Hooks;

/**
 * Removes front-end assets WordPress prints by default and a theme styled
 * with its own CSS does not use.
 */
final class CleanHead
{
    public function __construct(
        private readonly bool $blockStyles,
        private readonly bool $globalStyles,
        private readonly bool $emojis,
    ) {}

    /**
     * Runs on "init": unhooks the core callbacks before they enqueue anything.
     */
    public function unhookCoreAssets(): void
    {
        if ($this->globalStyles) {
            // Global styles (theme.json presets) are enqueued in the head and again in the footer.
            remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
            remove_action('wp_footer', 'wp_enqueue_global_styles', 1);
            remove_action('wp_enqueue_scripts', 'wp_enqueue_classic_theme_styles');
        }

        if ($this->emojis) {
            remove_action('wp_head', 'print_emoji_detection_script', 7);
            remove_action('wp_enqueue_scripts', 'wp_enqueue_emoji_styles');
            remove_action('wp_print_styles', 'print_emoji_styles');
            remove_action('admin_print_scripts', 'print_emoji_detection_script');
            remove_action('admin_print_styles', 'print_emoji_styles');
        }
    }

    /**
     * Runs late on "wp_enqueue_scripts".
     */
    public function dequeueBlockStyles(): void
    {
        if (! $this->blockStyles) {
            return;
        }

        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wp-block-library-theme');
    }
}
