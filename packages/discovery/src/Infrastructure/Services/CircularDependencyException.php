<?php

declare(strict_types=1);

namespace Meatpress\Discovery\Infrastructure\Services;

/**
 * Exception thrown when circular dependency is detected during instantiation
 */
class CircularDependencyException extends \RuntimeException {}
