<?php

declare(strict_types=1);

namespace Meatpress\BlockBinding\Tests\Fixtures;

use Meatpress\BlockBinding\Domain\Contracts\BindingEventsInterface;
use Meatpress\BlockBinding\Domain\Events\BindingResolved;

final class RecordingEvents implements BindingEventsInterface
{
    /** @var list<BindingResolved> */
    public array $announced = [];

    public function isListening(): bool
    {
        return true;
    }

    public function dispatch(BindingResolved $event): void
    {
        $this->announced[] = $event;
    }
}
