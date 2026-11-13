<?php

declare(strict_types=1);

namespace Meatpress\Asset\Infrastructure\Laravel;

use Meatpress\Application\Application\Services\ConsoleDetectionService;
use Meatpress\Asset\Domain\Contracts\RuntimeContext;

/**
 * The command line, as Meatpress's console detection sees it.
 */
final readonly class LaravelRuntimeContext implements RuntimeContext
{
    public function __construct(private ConsoleDetectionService $console) {}

    public function isCommandLine(): bool
    {
        return $this->console->isConsole() || $this->console->isWpCli();
    }
}
