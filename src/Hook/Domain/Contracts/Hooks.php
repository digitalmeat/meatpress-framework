<?php

declare(strict_types=1);

namespace Meatpress\Hook\Domain\Contracts;

use Meatpress\Attributes\Attributable;

/**
 * Marker interface for hookable classes.
 *
 * Classes implementing this interface signal that they contain
 * hook-related PHP attributes (#[Action], #[Filter]) and can be
 * processed by the discovery system.
 */
interface Hooks extends Attributable {}
