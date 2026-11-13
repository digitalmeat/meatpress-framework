<?php

declare(strict_types=1);

namespace Meatpress\Ajax\Domain\Exception;

/**
 * Exception thrown when an invalid AJAX action is defined in the domain.
 */
class InvalidAjaxActionException extends \InvalidArgumentException {}
