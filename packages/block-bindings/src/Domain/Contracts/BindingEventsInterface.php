<?php

declare(strict_types=1);

namespace Meatpress\BlockBinding\Domain\Contracts;

use Meatpress\BlockBinding\Domain\Events\BindingResolved;

/**
 * Where the resolver tells debugging tools what it resolved. In a Meatpress
 * application it is Laravel's event dispatcher; without one, nothing listens.
 */
interface BindingEventsInterface
{
    /**
     * Whether anything listens to BindingResolved: the event is built only then.
     */
    public function isListening(): bool;

    public function dispatch(BindingResolved $event): void;
}
