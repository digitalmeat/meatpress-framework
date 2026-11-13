<?php

declare(strict_types=1);

namespace Tests\Unit\Conventions;

use Meatpress\Conventions\ConventionsServiceProvider;
use Meatpress\Hook\Domain\Contract\Action;
use Meatpress\Hook\Domain\Contract\Filter;
use Orchestra\Testbench\TestCase as Orchestra;
use Tests\Unit\Conventions\Fakes\FakeHooks;

abstract class ConventionsTestCase extends Orchestra
{
    protected FakeHooks $hooks;

    protected function getPackageProviders($app): array
    {
        // The framework is not booted here: its hook services are replaced by a
        // recorder before the Meatpress provider boots.
        $this->hooks = new FakeHooks;
        $app->instance(Action::class, $this->hooks);
        $app->instance(Filter::class, $this->hooks);

        return [ConventionsServiceProvider::class];
    }
}
