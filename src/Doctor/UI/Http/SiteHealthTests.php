<?php

declare(strict_types=1);

namespace Meatpress\Doctor\UI\Http;

use Meatpress\Doctor\Application\Services\Doctor;
use Meatpress\Doctor\Domain\Contracts\CheckInterface;
use Meatpress\Doctor\Domain\Enums\CheckStatus;
use Meatpress\Doctor\Domain\Enums\RunContext;
use Meatpress\Doctor\Domain\Models\CheckResult;

/**
 * The doctor's checks in WordPress's Site Health (Tools › Site Health).
 *
 * Site Health runs in an administrator's web request: the boot a visitor gets,
 * which is where the failures WP-CLI cannot see — blocks missing over HTTP —
 * actually happen.
 */
final readonly class SiteHealthTests
{
    private const array STATUSES = [
        'ok' => 'good',
        'warning' => 'recommended',
        'error' => 'critical',
        'skipped' => 'good',
    ];

    public function __construct(private Doctor $doctor) {}

    /**
     * Filter callback for `site_status_tests`.
     *
     * @param  array<string, mixed>  $tests
     * @return array<string, mixed>
     */
    public function register(array $tests): array
    {
        foreach ($this->doctor->checksFor(RunContext::Http) as $check) {
            $tests['direct']['meatpress_'.str_replace('-', '_', $check->id())] = [
                'label' => $check->label(),
                'test' => fn (): array => $this->result($check, $this->doctor->runCheck($check, RunContext::Http)),
            ];
        }

        return $tests;
    }

    /**
     * @return array<string, mixed>
     */
    public function result(CheckInterface $check, CheckResult $result): array
    {
        $description = '<p>'.esc_html($result->summary).'</p>';

        if ($result->details !== []) {
            $description .= '<ul>'.implode('', array_map(fn (string $detail): string => '<li><code>'.esc_html($detail).'</code></li>', $result->details)).'</ul>';
        }

        $actions = '';

        if ($result->fix !== null && in_array($result->status, [CheckStatus::Error, CheckStatus::Warning], true)) {
            $actions = '<p>'.esc_html__('To fix it:', 'meatpress').' <code>'.esc_html($result->fix).'</code></p>';
        }

        return [
            'label' => $result->status === CheckStatus::Ok ? $check->label() : $check->label().' — '.$result->summary,
            'status' => self::STATUSES[$result->status->value],
            'badge' => ['label' => 'Meatpress', 'color' => 'blue'],
            'description' => $description,
            'actions' => $actions,
            'test' => 'meatpress_'.str_replace('-', '_', $check->id()),
        ];
    }
}
