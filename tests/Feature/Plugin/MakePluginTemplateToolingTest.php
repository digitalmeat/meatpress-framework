<?php

declare(strict_types=1);

use Meatpress\Modules\Infrastructure\Services\ModuleScaffolderService;
use Meatpress\Plugin\UI\Console\MakePluginCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

describe('meatpress:make:plugin', function (): void {
    it('leaves the template packaging tooling in bin/ out of the new plugin', function (): void {
        $pluginsPath = sys_get_temp_dir().'/meatpress-make-plugin-'.uniqid();
        config()->set('plugin.path', $pluginsPath);

        $scaffolder = Mockery::mock(ModuleScaffolderService::class);
        $scaffolder->shouldReceive('downloadAndScaffold')
            ->once()
            ->withArgs(fn (...$arguments): bool => ($arguments['removeDirs'] ?? $arguments[7] ?? null) === ['bin'])
            ->andReturnTrue();
        $this->app->instance(ModuleScaffolderService::class, $scaffolder);

        $command = $this->app->make(MakePluginCommand::class);
        $command->setLaravel($this->app);

        $input = new ArrayInput(['name' => 'acme', '--repository' => 'meatpress/plugin-default']);
        $input->setInteractive(false);

        expect($command->run($input, new BufferedOutput))->toBe(0);
    });
});
