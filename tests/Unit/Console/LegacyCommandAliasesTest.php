<?php

declare(strict_types=1);

use Illuminate\Console\Attributes\Aliases;
use Illuminate\Console\Attributes\Signature;
use Meatpress\Block\UI\Console\MakeBlockCommand;
use Meatpress\Foundation\Console\Commands\MakeModelCommand;
use Meatpress\Hook\UI\Console\ActionMakeCommand;
use Meatpress\Hook\UI\Console\FilterMakeCommand;
use Meatpress\Plugin\UI\Console\MakePluginCommand;
use Meatpress\PostType\UI\Console\PostTypeMakeCommand;
use Meatpress\Taxonomy\UI\Console\TaxonomyMakeCommand;
use Meatpress\Theme\UI\Console\MakeThemeCommand;
use Meatpress\Theme\UI\Console\RemoveThemeCommand;
use Meatpress\WordPress\Commands\LaunchPadSetupCommand;
use Meatpress\WpCli\UI\Console\WpCliMakeCommand;

/**
 * Commands were renamed to the Laravel colon convention; the old names must keep
 * resolving, e.g. `meatpress:env-setup` in a project's composer.json scripts.
 *
 * @return array<int, string>
 */
function legacyCommandAliases(string $class): array
{
    $reflection = new ReflectionClass($class);

    $signature = $reflection->getAttributes(Signature::class)[0] ?? null;

    if ($signature !== null) {
        return $signature->newInstance()->aliases ?? [];
    }

    $aliases = $reflection->getAttributes(Aliases::class)[0] ?? null;

    return $aliases?->newInstance()->aliases ?? [];
}

it('keeps the pre-rename name as an alias', function (string $class, string $legacyName): void {
    expect(legacyCommandAliases($class))->toContain($legacyName);
})->with([
    [MakeBlockCommand::class, 'meatpress:make-block'],
    [MakeModelCommand::class, 'meatpress:make-model'],
    [ActionMakeCommand::class, 'meatpress:make-action'],
    [FilterMakeCommand::class, 'meatpress:make-filter'],
    [MakePluginCommand::class, 'meatpress:make-plugin'],
    [PostTypeMakeCommand::class, 'meatpress:make-posttype'],
    [TaxonomyMakeCommand::class, 'meatpress:make-taxonomy'],
    [MakeThemeCommand::class, 'meatpress:make-theme'],
    [RemoveThemeCommand::class, 'meatpress:delete-theme'],
    [LaunchPadSetupCommand::class, 'meatpress:env-setup'],
    [WpCliMakeCommand::class, 'meatpress:make-wp-cli'],
]);
