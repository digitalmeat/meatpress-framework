<?php

declare(strict_types=1);

namespace Meatpress\Events\WordPress\Option;

/**
 * Base class for all option-related events.
 *
 * This abstract class provides the foundation for all option events,
 * containing the option name and its old and new values.
 */
abstract class OptionEvent
{
    /**
     * Constructor.
     */
    public function __construct(
        public readonly string $optionName,
        public readonly mixed $oldValue,
        public readonly mixed $newValue
    ) {}
}
