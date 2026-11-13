<?php

declare(strict_types=1);

namespace Meatpress\Doctor\Infrastructure\Checks;

use Dotenv\Dotenv;
use Meatpress\Doctor\Domain\Contracts\CheckInterface;
use Meatpress\Doctor\Domain\Enums\RunContext;
use Meatpress\Doctor\Domain\Models\CheckResult;

/**
 * .env uses the names Meatpress reads.
 *
 * Meatpress reads Laravel's names. A .env written for Bedrock or a plain
 * wp-config.php — DB_NAME, DB_USER, WP_HOME — is ignored without an error, and
 * with DB_CONNECTION unset Laravel falls back to sqlite: the site then opens a
 * file named after the database instead of connecting to it.
 */
final readonly class EnvironmentFileCheck implements CheckInterface
{
    /** Names Meatpress does not read => the one it reads instead. */
    private const array RENAMED = [
        'DB_NAME' => 'DB_DATABASE',
        'DB_USER' => 'DB_USERNAME',
        'WP_HOME' => 'APP_URL',
        'WP_SITEURL' => 'APP_URL',
    ];

    public function id(): string
    {
        return 'environment-file';
    }

    public function label(): string
    {
        return 'Environment file';
    }

    public function runsIn(): array
    {
        return [RunContext::Console, RunContext::Http];
    }

    public function run(RunContext $context): CheckResult
    {
        $file = base_path('.env');

        if (! is_file($file)) {
            return CheckResult::skipped('No .env file: the settings come from the environment.');
        }

        $env = Dotenv::parse((string) file_get_contents($file));
        $errors = [];
        $warnings = [];

        foreach (self::RENAMED as $ignored => $read) {
            if (! array_key_exists($ignored, $env)) {
                continue;
            }

            if (! array_key_exists($read, $env)) {
                $errors[] = sprintf('%s is not read: Meatpress reads %s', $ignored, $read);
            } elseif ($env[$ignored] !== $env[$read]) {
                $warnings[] = sprintf('%s (%s) is ignored: Meatpress uses %s (%s)', $ignored, $env[$ignored], $read, $env[$read]);
            }
        }

        if (config('database.default') === 'sqlite' && ($env['DB_HOST'] ?? '') !== '') {
            $errors[] = 'DB_HOST is set, but the connection is sqlite: DB_CONNECTION is missing or not mysql';
        }

        if ($errors !== []) {
            return CheckResult::error(
                'Some settings in .env are not the ones Meatpress reads.',
                [...$errors, ...$warnings],
                "Use Laravel's names in .env: DB_CONNECTION=mysql, DB_DATABASE, DB_USERNAME, APP_URL",
            );
        }

        if ($warnings !== []) {
            return CheckResult::warning('.env carries settings Meatpress ignores.', $warnings, 'Remove them, or give them the value Meatpress uses');
        }

        return CheckResult::ok('.env uses the names Meatpress reads.');
    }
}
