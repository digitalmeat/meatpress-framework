<?php

declare(strict_types=1);

namespace Meatpress\Application\Application\Services;

use Meatpress\Application\Domain\Contracts\ConsoleDetectorInterface;

/**
 * Application service to orchestrate console detection use cases.
 */
class ConsoleDetectionService
{
    /**
     * Constructor.
     */
    public function __construct(protected ConsoleDetectorInterface $detector) {}

    /**
     * Check if running in console.
     */
    public function isConsole(): bool
    {
        return $this->detector->isConsole();
    }

    /**
     * Check if running in WP CLI.
     */
    public function isWpCli(): bool
    {
        return $this->detector->isWpCli();
    }
}
