<?php

declare(strict_types=1);

namespace Meatpress\Option\Domain\Exception;

use Meatpress\Option\Domain\Service\OptionValidationService;

/**
 * Exception thrown when an option key or value is invalid.
 *
 * Raised by {@see OptionValidationService}
 * during key/value validation.
 */
final class InvalidOptionException extends \Exception {}
