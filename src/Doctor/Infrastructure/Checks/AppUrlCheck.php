<?php

declare(strict_types=1);

namespace Meatpress\Doctor\Infrastructure\Checks;

use Meatpress\Doctor\Domain\Contracts\CheckInterface;
use Meatpress\Doctor\Domain\Enums\RunContext;
use Meatpress\Doctor\Domain\Models\CheckResult;
use Meatpress\WordPress\AppUrl;

/**
 * APP_URL names a scheme.
 *
 * `APP_URL=meatpress.test` in a hand-edited .env is read as http://meatpress.test,
 * but everything else that reads the setting (Laravel's URL helpers in the
 * console, mails, a queue worker) gets the bare host.
 */
final readonly class AppUrlCheck implements CheckInterface
{
    public function id(): string
    {
        return 'app-url';
    }

    public function label(): string
    {
        return 'Application URL';
    }

    public function runsIn(): array
    {
        return [RunContext::Console, RunContext::Http];
    }

    public function run(RunContext $context): CheckResult
    {
        $url = trim((string) config('app.url'));

        if ($url === '') {
            return CheckResult::skipped('APP_URL is not set.');
        }

        if (! AppUrl::hasScheme($url)) {
            return CheckResult::warning(
                sprintf('APP_URL (%s) has no scheme.', $url),
                [sprintf('Meatpress reads it as %s', AppUrl::normalize($url))],
                sprintf('Write APP_URL=%s (https:// when the site is served over TLS)', AppUrl::normalize($url)),
            );
        }

        return CheckResult::ok('APP_URL names a scheme.');
    }
}
