<?php

declare(strict_types=1);

namespace Tests\Unit\Conventions\Fakes;

use Meatpress\Hook\Async\PendingAsync;
use Meatpress\Hook\Domain\Contract\Action;
use Meatpress\Hook\Domain\Contract\Filter;

/**
 * Records the hooks registered through the framework's Action and Filter services.
 */
final class FakeHooks implements Action, Filter
{
    /**
     * @var array<string, list<array{callback: mixed, priority: int}>>
     */
    public array $registered = [];

    public function add(string|array $hooks, callable|string|array $callback, int $priority = 10, ?int $acceptedArgs = null): static
    {
        foreach ((array) $hooks as $hook) {
            $this->registered[$hook][] = ['callback' => $callback, 'priority' => $priority];
        }

        return $this;
    }

    public function has(string $hook): bool
    {
        return isset($this->registered[$hook]);
    }

    public function remove(string $hook, callable|string|array|null $callback = null, int $priority = 10): static|false
    {
        unset($this->registered[$hook]);

        return $this;
    }

    public function exists(string $hook): bool
    {
        return $this->has($hook);
    }

    public function callbacks(string $hook): ?array
    {
        return $this->registered[$hook] ?? null;
    }

    public function do(string $hook, mixed ...$args): static
    {
        return $this;
    }

    public function apply(string $hook, mixed $value, mixed ...$args): mixed
    {
        return $value;
    }

    public function async(string|array $except = []): PendingAsync
    {
        throw new \LogicException('Not supported by the fake.');
    }
}
