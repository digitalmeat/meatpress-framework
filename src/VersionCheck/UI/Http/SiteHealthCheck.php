<?php

declare(strict_types=1);

namespace Meatpress\VersionCheck\UI\Http;

use Meatpress\VersionCheck\Domain\Services\VersionComparator;

/**
 * Integrates Meatpress version information into WordPress Site Health.
 *
 * Provides two integration points:
 * - **Debug Information** (`Tools > Site Health > Info`): Displays the installed
 *   and latest Meatpress version in a dedicated "Meatpress" section.
 * - **Status Tests** (`Tools > Site Health > Status`): Adds a test that reports
 *   whether Meatpress is up to date, with actionable links to the changelog.
 *
 * @see https://developer.wordpress.org/reference/hooks/debug_information/
 * @see https://developer.wordpress.org/reference/hooks/site_status_tests/
 */
class SiteHealthCheck
{
    public function __construct(
        private readonly VersionComparator $comparator
    ) {}

    /**
     * Add a "Meatpress" section to the Site Health debug information page.
     *
     * Displays the installed version, latest available version, and whether
     * the framework is up to date.
     *
     * @param  array  $info  Existing debug information sections
     * @return array Modified debug information with the Meatpress section appended
     */
    public function addDebugInfo(array $info): array
    {
        $current = $this->comparator->getCurrentVersion() ?? 'Unknown';
        $latest = $this->comparator->getLatestVersion() ?? 'Unable to check';

        $info['meatpress'] = [
            'label' => 'Meatpress',
            'fields' => [
                'version' => [
                    'label' => __('Installed version', 'meatpress'),
                    'value' => $current,
                ],
                'latest_version' => [
                    'label' => __('Latest stable version', 'meatpress'),
                    'value' => $latest,
                ],
                'up_to_date' => [
                    'label' => __('Up to date', 'meatpress'),
                    'value' => $this->comparator->isUpdateAvailable() ? __('No', 'meatpress') : __('Yes', 'meatpress'),
                ],
            ],
        ];

        return $info;
    }

    /**
     * Register the Meatpress version check as a direct Site Health status test.
     *
     * The test appears under the "Status" tab and calls {@see testVersionStatus()}
     * to determine whether the framework needs updating.
     *
     * @param  array  $tests  Existing status tests
     * @return array Modified status tests with the Meatpress check appended
     */
    public function addTests(array $tests): array
    {
        $tests['direct']['meatpress_update'] = [
            'label' => __('Meatpress is up to date', 'meatpress'),
            'test' => $this->testVersionStatus(...),
        ];

        return $tests;
    }

    /**
     * Execute the version status test for Site Health.
     *
     * Returns one of three results:
     * - **good** (blue badge): Meatpress is up to date
     * - **recommended** (orange badge): A newer version is available
     * - **recommended** (orange badge): Version status could not be determined
     * - **good** (blue badge): A development build is installed, not compared with releases
     *
     * @return array{label: string, status: string, badge: array{label: string, color: string}, description: string, test: string, actions?: string} Test result
     */
    public function testVersionStatus(): array
    {
        $current = $this->comparator->getCurrentVersion();
        $latest = $this->comparator->getLatestVersion();

        if ($current === null || $latest === null) {
            return [
                'label' => __('Unable to determine Meatpress version status', 'meatpress'),
                'status' => 'recommended',
                'badge' => [
                    'label' => 'Meatpress',
                    'color' => 'orange',
                ],
                'description' => '<p>'.__('Could not determine the current or latest Meatpress version. Check your internet connection.', 'meatpress').'</p>',
                'test' => 'meatpress_update',
            ];
        }

        if ($this->comparator->isDevelopmentBuild()) {
            return [
                'label' => __('Meatpress is running a development build', 'meatpress'),
                'status' => 'good',
                'badge' => [
                    'label' => 'Meatpress',
                    'color' => 'blue',
                ],
                /* translators: 1: installed development build, 2: latest stable version */
                'description' => '<p>'.sprintf(__('You are running Meatpress %1$s, which is not compared with releases. The latest stable version is %2$s.', 'meatpress'), $current, $latest).'</p>',
                'test' => 'meatpress_update',
            ];
        }

        if (! $this->comparator->isUpdateAvailable()) {
            return [
                'label' => __('Meatpress is up to date', 'meatpress'),
                'status' => 'good',
                'badge' => [
                    'label' => 'Meatpress',
                    'color' => 'blue',
                ],
                /* translators: %s: currently installed version */
                'description' => '<p>'.sprintf(__('You are running Meatpress %s, which is the latest version.', 'meatpress'), $current).'</p>',
                'test' => 'meatpress_update',
            ];
        }

        return [
            /* translators: %s: latest available version */
            'label' => sprintf(__('Meatpress %s is available', 'meatpress'), $latest),
            'status' => 'recommended',
            'badge' => [
                'label' => 'Meatpress',
                'color' => 'orange',
            ],
            'description' => '<p>'.sprintf(
                /* translators: 1: current version, 2: latest version, 3: opening link tag, 4: closing link tag */
                __('You are running Meatpress %1$s. The latest version is %2$s. %3$sView changelog%4$s.', 'meatpress'),
                $current,
                $latest,
                '<a href="'.esc_url('https://github.com/digitalmeat/meatpress-framework/releases/tag/v'.$latest).'" target="_blank" rel="noopener noreferrer">',
                '</a>'
            ).'</p>',
            'actions' => '<p><a href="'.esc_url('https://github.com/digitalmeat/meatpress-framework/blob/main/CHANGELOG.md').'" target="_blank" rel="noopener noreferrer">'.__('Learn more about updating Meatpress', 'meatpress').'</a></p>',
            'test' => 'meatpress_update',
        ];
    }
}
