<?php

declare(strict_types=1);

namespace Meatpress\Conventions\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Writes the WordPress authentication keys and salts to the .env file.
 *
 * Without them, WordPress falls back to random values stored in the database,
 * which change with every fresh database and log every user out.
 */
final class GenerateSaltsCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'salts:generate {--show : Display the salts instead of writing them}
                                           {--force : Replace the salts already set}';

    /**
     * @var string
     */
    protected $description = 'Generate the WordPress authentication keys and salts in the .env file';

    /**
     * @var list<string>
     */
    private const array KEYS = [
        'AUTH_KEY',
        'SECURE_AUTH_KEY',
        'LOGGED_IN_KEY',
        'NONCE_KEY',
        'AUTH_SALT',
        'SECURE_AUTH_SALT',
        'LOGGED_IN_SALT',
        'NONCE_SALT',
    ];

    public function handle(): int
    {
        $salts = [];

        foreach (self::KEYS as $key) {
            // Alphanumeric only: no quoting or escaping issues in the .env file.
            $salts[$key] = Str::random(64);
        }

        if ($this->option('show')) {
            foreach ($salts as $key => $value) {
                $this->line("{$key}={$value}");
            }

            return self::SUCCESS;
        }

        $path = $this->laravel->environmentFilePath();

        if (! is_file($path)) {
            $this->components->error('The .env file does not exist.');

            return self::FAILURE;
        }

        $content = (string) file_get_contents($path);

        $written = 0;

        foreach ($salts as $key => $value) {
            $line = "{$key}={$value}";
            $pattern = '/^'.preg_quote($key, '/').'=.*$/m';

            // Replacing a salt logs every user out: keep the ones already set.
            if (! $this->option('force') && preg_match('/^'.preg_quote($key, '/').'=.+$/m', $content)) {
                continue;
            }

            $written++;

            $content = preg_match($pattern, $content)
                ? (string) preg_replace($pattern, $line, $content)
                : rtrim($content, "\n")."\n".$line."\n";
        }

        file_put_contents($path, $content);

        $written > 0
            ? $this->components->info("{$written} WordPress salt(s) written to the .env file.")
            : $this->components->info('WordPress salts already set. Use --force to replace them.');

        return self::SUCCESS;
    }
}
