<?php

declare(strict_types=1);

namespace Meatpress\Dashboard\UI\Http;

use Meatpress\Dashboard\Domain\Services\SystemInfoCollector;
use Meatpress\Modules\UI\Http\ModulesAdminPage;

/**
 * Renders the Meatpress admin dashboard page.
 *
 * Displays system information including framework version, environment details,
 * discovered entities, performance stats, and cache state, with Meatpress branding.
 */
final readonly class DashboardController
{
    /**
     * Meatpress website, home of the documentation.
     */
    private const string WEBSITE_URL = 'https://github.com/digitalmeat/meatpress-docs';

    public function __construct(
        private SystemInfoCollector $collector
    ) {}

    public function __invoke(): void
    {
        $info = $this->collector->collect();

        echo '<div class="wrap meatpress-wrap">';

        $this->renderStyles();
        $this->renderHeader($info['framework']);

        echo '<div class="meatpress-dashboard">';

        $this->renderEnvironmentCard($info['environment']);
        $this->renderWordPressCard($info['wordpress']);
        $this->renderPostTypesCard($info['discovery']['post_types']);
        $this->renderTaxonomiesCard($info['discovery']['taxonomies']);
        $this->renderHooksCard($info['discovery']['hooks']);
        $this->renderRestRoutesCard($info['discovery']['rest_routes']);
        $this->renderWpCliCard($info['discovery']['wp_cli_commands']);
        $this->renderSchedulesCard($info['discovery']['schedules']);
        $this->renderServiceProvidersCard($info['discovery']['service_providers']);
        $this->renderModulesCard($info['modules']);
        $this->renderCacheCard($info['cache']);
        $this->renderPerformanceCard($info['performance']);
        $this->renderThemeCard($info['theme']);
        $this->renderTranslationsCard($info['translations']);

        echo '</div>';
        echo '</div>';
    }

    private function renderStyles(): void
    {
        echo '<style>
            .meatpress-wrap { max-width: 1200px; }
            .meatpress-header {
                display: flex; align-items: center; gap: 20px;
                background: linear-gradient(135deg, #1d142a 0%, #2d1f3d 100%);
                border-radius: 8px; padding: 24px 32px; margin-bottom: 24px;
            }
            .meatpress-header-logo { flex-shrink: 0; max-width: 200px; }
            .meatpress-header-logo svg { width: 100%; height: auto; display: block; }
            .meatpress-header-info { flex: 1; }
            .meatpress-docs-link { margin-left: 12px; font-size: 13px; }
            .meatpress-header .meatpress-version {
                font-size: 13px; font-weight: 500; padding: 3px 10px; border-radius: 12px; display: inline-block;
            }
            .meatpress-version--current {
                background: linear-gradient(135deg, #fb196d, #ff8c12); color: #fff;
            }
            .meatpress-version--update {
                background: #fff3cd; color: #856404;
            }
            .meatpress-dashboard {
                display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
                gap: 16px;
            }
            .meatpress-card {
                background: #fff; border: 1px solid #e0e0e0; border-radius: 6px;
                padding: 20px; position: relative; overflow: hidden;
            }
            .meatpress-card::before {
                content: ""; position: absolute; top: 0; left: 0; right: 0; height: 3px;
                background: linear-gradient(90deg, #fb196d, #ff314c, #ff5334, #ff8c12);
            }
            .meatpress-card h2 {
                margin: 0 0 14px; padding: 0; font-size: 13px; font-weight: 600;
                color: #1d142a; text-transform: uppercase; letter-spacing: 0.5px;
            }
            .meatpress-card table { width: 100%; border-collapse: collapse; }
            .meatpress-card td { padding: 7px 0; border-bottom: 1px solid #f5f5f5; font-size: 13px; }
            .meatpress-card tr:last-child td { border-bottom: none; }
            .meatpress-card td:first-child { color: #666; width: 40%; }
            .meatpress-card td:last-child { color: #1d142a; font-weight: 500; }
            .meatpress-badge {
                display: inline-block; padding: 2px 10px; border-radius: 10px;
                font-size: 12px; font-weight: 600; line-height: 1.5;
            }
            .meatpress-badge--gradient { background: linear-gradient(135deg, #fb196d, #ff8c12); color: #fff; }
            .meatpress-badge--green { background: #e8f5e9; color: #2e7d32; }
            .meatpress-badge--orange { background: #fff3e0; color: #e65100; }
            .meatpress-badge--gray { background: #f5f5f5; color: #616161; }
            .meatpress-entity-list { margin: 0; padding: 0; list-style: none; }
            .meatpress-entity-item {
                display: flex; align-items: center; justify-content: space-between;
                padding: 8px 0; border-bottom: 1px solid #f5f5f5; gap: 8px;
            }
            .meatpress-entity-item:last-child { border-bottom: none; }
            .meatpress-entity-label { font-size: 13px; font-weight: 500; color: #1d142a; }
            .meatpress-entity-meta { font-size: 11px; color: #999; word-break: break-all; }
            .meatpress-entity-slug {
                font-size: 11px; background: #f5f5f5; color: #666;
                padding: 2px 8px; border-radius: 3px; font-family: monospace; white-space: nowrap; flex-shrink: 0;
            }
            .meatpress-empty { color: #999; font-style: italic; font-size: 13px; }
        </style>';
    }

    /**
     * @param  array{current: ?string, latest: ?string, update_available: bool, development: bool}  $framework
     */
    private function renderHeader(array $framework): void
    {
        $current = $framework['current'] ?? __('Unknown', 'meatpress');
        $updateAvailable = $framework['update_available'];
        $isDev = $framework['development'];

        $logoPath = dirname(__DIR__, 4).'/resources/images/meatpress-logo.svg';

        echo '<div class="meatpress-header">';

        if (file_exists($logoPath)) {
            echo '<div class="meatpress-header-logo">';
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted SVG from framework package
            echo file_get_contents($logoPath);
            echo '</div>';
        }

        echo '<div class="meatpress-header-info">';

        if ($isDev) {
            printf(
                '<span class="meatpress-version meatpress-version--current">%s</span>',
                esc_html((string) $current)
            );
        } elseif ($updateAvailable) {
            $latest = $framework['latest'] ?? '';
            printf(
                '<span class="meatpress-version meatpress-version--update">v%s &rarr; v%s %s</span>',
                esc_html((string) $current),
                esc_html($latest),
                __('available', 'meatpress')
            );
        } else {
            printf(
                '<span class="meatpress-version meatpress-version--current">v%s</span>',
                esc_html((string) $current)
            );
        }

        printf(
            '<a class="meatpress-docs-link" href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
            esc_url(self::WEBSITE_URL),
            esc_html__('Documentation', 'meatpress')
        );

        echo '</div>';
        echo '</div>';
    }

    /**
     * @param  array{php: string, laravel: string, wordpress: string}  $environment
     */
    private function renderEnvironmentCard(array $environment): void
    {
        echo '<div class="meatpress-card">';
        echo '<h2>'.__('Environment', 'meatpress').'</h2>';
        echo '<table>';
        printf('<tr><td>PHP</td><td>%s</td></tr>', esc_html($environment['php']));
        printf('<tr><td>Laravel</td><td>%s</td></tr>', esc_html($environment['laravel']));
        printf('<tr><td>WordPress</td><td>%s</td></tr>', esc_html($environment['wordpress']));
        echo '</table>';
        echo '</div>';
    }

    /**
     * @param  array{debug: bool, multisite: bool, permalink_structure: string}  $wordpress
     */
    private function renderWordPressCard(array $wordpress): void
    {
        $debugBadge = $wordpress['debug']
            ? '<span class="meatpress-badge meatpress-badge--orange">'.__('Enabled', 'meatpress').'</span>'
            : '<span class="meatpress-badge meatpress-badge--green">'.__('Disabled', 'meatpress').'</span>';

        $multisiteBadge = $wordpress['multisite']
            ? '<span class="meatpress-badge meatpress-badge--gradient">'.__('Yes', 'meatpress').'</span>'
            : '<span class="meatpress-badge meatpress-badge--gray">'.__('No', 'meatpress').'</span>';

        echo '<div class="meatpress-card">';
        echo '<h2>'.__('WordPress Config', 'meatpress').'</h2>';
        echo '<table>';
        printf('<tr><td>WP_DEBUG</td><td>%s</td></tr>', $debugBadge);
        printf('<tr><td>%s</td><td>%s</td></tr>', __('Multisite', 'meatpress'), $multisiteBadge);
        printf('<tr><td>%s</td><td><code>%s</code></td></tr>', __('Permalinks', 'meatpress'), esc_html($wordpress['permalink_structure']));
        echo '</table>';
        echo '</div>';
    }

    /**
     * @param  array{count: int, items: list<array{class: string, slug: string, label: string}>}  $postTypes
     */
    private function renderPostTypesCard(array $postTypes): void
    {
        $this->renderEntityCard(__('Post Types', 'meatpress'), $postTypes, true);
    }

    /**
     * @param  array{count: int, items: list<array{class: string, slug: string, label: string}>}  $taxonomies
     */
    private function renderTaxonomiesCard(array $taxonomies): void
    {
        $this->renderEntityCard(__('Taxonomies', 'meatpress'), $taxonomies, true);
    }

    /**
     * @param  array{count: int, items: list<array{class: string, slug?: string, label?: string, method?: string}>}  $data
     */
    private function renderEntityCard(string $title, array $data, bool $hasSlugAndLabel = false): void
    {
        echo '<div class="meatpress-card">';
        printf(
            '<h2>%s <span class="meatpress-badge meatpress-badge--gradient">%d</span></h2>',
            $title,
            $data['count']
        );

        if ($data['items'] === []) {
            printf('<p class="meatpress-empty">%s</p>', __('None discovered.', 'meatpress'));
        } else {
            echo '<ul class="meatpress-entity-list">';
            foreach ($data['items'] as $item) {
                echo '<li class="meatpress-entity-item">';
                echo '<div>';

                if ($hasSlugAndLabel && isset($item['label'])) {
                    printf('<div class="meatpress-entity-label">%s</div>', esc_html($item['label']));
                }

                $meta = $item['class'];
                if (isset($item['method'])) {
                    $meta .= '::'.$item['method'].'()';
                }

                printf('<div class="meatpress-entity-meta">%s</div>', esc_html($meta));

                echo '</div>';

                if ($hasSlugAndLabel && isset($item['slug'])) {
                    printf('<span class="meatpress-entity-slug">%s</span>', esc_html($item['slug']));
                }

                echo '</li>';
            }

            echo '</ul>';
        }

        echo '</div>';
    }

    /**
     * @param  array{count: int, actions: int, filters: int}  $hooks
     */
    private function renderHooksCard(array $hooks): void
    {
        echo '<div class="meatpress-card">';
        printf(
            '<h2>%s <span class="meatpress-badge meatpress-badge--gradient">%d</span></h2>',
            __('Hooks', 'meatpress'),
            $hooks['count']
        );
        echo '<table>';
        printf('<tr><td>%s</td><td>%d</td></tr>', __('Actions', 'meatpress'), $hooks['actions']);
        printf('<tr><td>%s</td><td>%d</td></tr>', __('Filters', 'meatpress'), $hooks['filters']);
        echo '</table>';
        echo '</div>';
    }

    /**
     * @param  array{count: int, items: list<array{class: string}>}  $restRoutes
     */
    private function renderRestRoutesCard(array $restRoutes): void
    {
        $this->renderEntityCard(__('REST API Routes', 'meatpress'), $restRoutes);
    }

    /**
     * @param  array{count: int, items: list<array{class: string}>}  $wpCli
     */
    private function renderWpCliCard(array $wpCli): void
    {
        $this->renderEntityCard(__('WP-CLI Commands', 'meatpress'), $wpCli);
    }

    /**
     * @param  array{count: int, items: list<array{class: string, method: string}>}  $schedules
     */
    private function renderSchedulesCard(array $schedules): void
    {
        $this->renderEntityCard(__('Scheduled Tasks', 'meatpress'), $schedules);
    }

    /**
     * @param  array{count: int, items: list<array{class: string}>}  $providers
     */
    private function renderServiceProvidersCard(array $providers): void
    {
        $this->renderEntityCard(__('Auto-discovered Providers', 'meatpress'), $providers);
    }

    /**
     * @param  array{count: int, enabled: int, disabled: int, connector: string|null, items: list<array{name: string, status: string, description: string, priority: string, version: string|null, latest: string|null}>}  $modules
     */
    private function renderModulesCard(array $modules): void
    {
        echo '<div class="meatpress-card">';
        printf(
            '<h2>%s <span class="meatpress-badge meatpress-badge--gradient">%d</span></h2>',
            __('Modules', 'meatpress'),
            $modules['count']
        );

        if ($modules['items'] === []) {
            printf('<p class="meatpress-empty">%s</p>', __('No modules installed.', 'meatpress'));
        } else {
            echo '<table>';
            printf(
                '<tr><td>%s</td><td><span class="meatpress-badge meatpress-badge--green">%d</span></td></tr>',
                __('Enabled', 'meatpress'),
                $modules['enabled']
            );
            printf(
                '<tr><td>%s</td><td><span class="meatpress-badge meatpress-badge--gray">%d</span></td></tr>',
                __('Disabled', 'meatpress'),
                $modules['disabled']
            );

            if ($modules['connector'] !== null) {
                printf(
                    '<tr><td>%s</td><td>%s</td></tr>',
                    __('Stored in', 'meatpress'),
                    esc_html($modules['connector'])
                );
            }

            echo '</table>';

            echo '<ul class="meatpress-entity-list" style="margin-top: 10px;">';
            foreach ($modules['items'] as $module) {
                $statusBadge = $module['status'] === 'enabled'
                    ? '<span class="meatpress-badge meatpress-badge--green">'.__('On', 'meatpress').'</span>'
                    : '<span class="meatpress-badge meatpress-badge--gray">'.__('Off', 'meatpress').'</span>';

                echo '<li class="meatpress-entity-item">';
                echo '<div>';
                printf('<div class="meatpress-entity-label">%s</div>', esc_html($module['name']));
                if ($module['description'] !== '') {
                    printf('<div class="meatpress-entity-meta">%s</div>', esc_html($module['description']));
                }

                echo '</div>';
                echo $statusBadge;
                echo '</li>';
            }

            echo '</ul>';

            printf(
                '<p><a href="%s">%s</a></p>',
                esc_url(admin_url('plugins.php?page='.ModulesAdminPage::SLUG)),
                esc_html__('Manage modules', 'meatpress')
            );
        }

        echo '</div>';
    }

    /**
     * @param  array{driver: string, enabled: bool}  $cache
     */
    private function renderCacheCard(array $cache): void
    {
        $statusBadge = $cache['enabled']
            ? '<span class="meatpress-badge meatpress-badge--green">'.__('Enabled', 'meatpress').'</span>'
            : '<span class="meatpress-badge meatpress-badge--gray">'.__('Disabled', 'meatpress').'</span>';

        echo '<div class="meatpress-card">';
        echo '<h2>'.__('Discovery Cache', 'meatpress').'</h2>';
        echo '<table>';
        printf('<tr><td>%s</td><td>%s</td></tr>', __('Driver', 'meatpress'), esc_html($cache['driver']));
        printf('<tr><td>%s</td><td>%s</td></tr>', __('Status', 'meatpress'), $statusBadge);
        echo '</table>';
        echo '</div>';
    }

    /**
     * @param  array<string, mixed>  $performance
     */
    private function renderPerformanceCard(array $performance): void
    {
        echo '<div class="meatpress-card">';
        echo '<h2>'.__('Discovery Performance', 'meatpress').'</h2>';

        if ($performance === []) {
            printf('<p class="meatpress-empty">%s</p>', __('No performance data available.', 'meatpress'));
        } else {
            echo '<table>';

            $context = $performance['context'] ?? [];
            if (isset($context['cache_hits'])) {
                printf('<tr><td>%s</td><td>%d</td></tr>', __('Cache hits', 'meatpress'), $context['cache_hits']);
            }

            if (isset($context['cache_misses'])) {
                printf('<tr><td>%s</td><td>%d</td></tr>', __('Cache misses', 'meatpress'), $context['cache_misses']);
            }

            if (isset($context['classes_processed'])) {
                printf('<tr><td>%s</td><td>%d</td></tr>', __('Classes processed', 'meatpress'), $context['classes_processed']);
            }

            if (isset($context['discoveries_executed'])) {
                printf('<tr><td>%s</td><td>%d</td></tr>', __('Discoveries executed', 'meatpress'), $context['discoveries_executed']);
            }

            if (isset($performance['instance_pool']['total_instances'])) {
                printf('<tr><td>%s</td><td>%d</td></tr>', __('Instance pool size', 'meatpress'), $performance['instance_pool']['total_instances']);
            }

            if (isset($performance['static_cache_size'])) {
                printf('<tr><td>%s</td><td>%d</td></tr>', __('Static cache entries', 'meatpress'), $performance['static_cache_size']);
            }

            echo '</table>';
        }

        echo '</div>';
    }

    /**
     * @param  array{name: string, version: string, template: string}  $theme
     */
    private function renderThemeCard(array $theme): void
    {
        echo '<div class="meatpress-card">';
        echo '<h2>'.__('Active Theme', 'meatpress').'</h2>';
        echo '<table>';
        printf('<tr><td>%s</td><td>%s</td></tr>', __('Name', 'meatpress'), esc_html($theme['name']));
        printf('<tr><td>%s</td><td>%s</td></tr>', __('Version', 'meatpress'), esc_html($theme['version']));
        printf('<tr><td>%s</td><td><code>%s</code></td></tr>', __('Template', 'meatpress'), esc_html($theme['template']));
        echo '</table>';
        echo '</div>';
    }

    /**
     * @param  array{override_active: bool, helper_file: string, wordpress_locale: string, laravel_locale: string}  $translations
     */
    private function renderTranslationsCard(array $translations): void
    {
        $overrideBadge = $translations['override_active']
            ? '<span class="meatpress-badge meatpress-badge--green">'.__('Active', 'meatpress').'</span>'
            : '<span class="meatpress-badge meatpress-badge--orange">'.__('Inactive', 'meatpress').'</span>';

        echo '<div class="meatpress-card">';
        echo '<h2>'.__('Translations', 'meatpress').'</h2>';
        echo '<table>';
        printf('<tr><td>%s</td><td>%s</td></tr>', __('__() override', 'meatpress'), $overrideBadge);
        printf('<tr><td>%s</td><td><code>%s</code></td></tr>', __('WordPress locale', 'meatpress'), esc_html($translations['wordpress_locale']));
        printf('<tr><td>%s</td><td><code>%s</code></td></tr>', __('Laravel locale', 'meatpress'), esc_html($translations['laravel_locale']));
        echo '</table>';

        if (! $translations['override_active']) {
            // Silent otherwise: with Laravel's __() in place, every WordPress
            // catalogue lookup returns the untranslated string.
            printf(
                '<p><strong>%s</strong><br><code>%s</code></p>',
                esc_html__('WordPress catalogues will not resolve. __() resolves from:', 'meatpress'),
                esc_html($translations['helper_file'])
            );
        }

        echo '</div>';
    }
}
