<?php

declare(strict_types=1);

namespace Meatpress\Modules\Domain\Enums;

use Meatpress\Plugin\Application\Services\PluginRegistrar;
use Meatpress\Theme\Domain\Contracts\ThemeRegistrarInterface;

/**
 * Supported module types for Meatpress framework registration.
 *
 * Used by the {@see \meatpress_register()} helper to identify which registrar
 * should handle the module. Each case maps to a specific registration service:
 *
 * - **Theme**: Registered via {@see ThemeRegistrarInterface}.
 *   Auto-detects the active theme name and path from WordPress.
 *
 * - **Plugin**: Registered via {@see PluginRegistrar}.
 *   Requires an explicit plugin name and directory path.
 *
 * @example
 * ```php
 * use Meatpress\Modules\Domain\Enums\ModuleType;
 *
 * // In a theme's functions.php
 * meatpress_register(ModuleType::Theme);
 *
 * // In a plugin's main file
 * meatpress_register(ModuleType::Plugin, 'my-plugin', __DIR__);
 * ```
 */
enum ModuleType: string
{
    /** WordPress theme — auto-detected from the active stylesheet. */
    case Theme = 'theme';

    /** WordPress plugin — requires explicit name and path. */
    case Plugin = 'plugin';
}
