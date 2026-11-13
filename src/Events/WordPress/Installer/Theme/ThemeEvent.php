<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Installer\Theme;

use Meatpress\Events\WordPress\Installer\InstallerEvent;

/**
 * Base class for all theme-related events.
 *
 * This abstract class extends the InstallerEvent class and provides
 * the foundation for all theme-specific events.
 */
abstract class ThemeEvent extends InstallerEvent {}
