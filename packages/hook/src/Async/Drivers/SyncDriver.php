<?php

declare(strict_types=1);

namespace Meatpress\Hook\Async\Drivers;

use Meatpress\Hook\Async\Async;
use Meatpress\Hook\Async\AsyncPayload;
use Meatpress\Hook\Async\Contracts\AsyncDriver;

/**
 * Runs the handler immediately, in the request.
 *
 * The payload still goes through its JSON form, so an argument that could not
 * travel fails here too, in development, rather than only once deployed.
 */
final class SyncDriver implements AsyncDriver
{
    public function available(): bool
    {
        return true;
    }

    public function dispatch(AsyncPayload $payload, int $delay = 0): void
    {
        Async::receive($payload->toJson());
    }
}
