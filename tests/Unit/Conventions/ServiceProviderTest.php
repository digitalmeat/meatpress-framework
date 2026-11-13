<?php

declare(strict_types=1);

namespace Tests\Unit\Conventions;

use Illuminate\Support\Facades\Artisan;

final class ServiceProviderTest extends ConventionsTestCase
{
    public function test_default_configuration_is_merged(): void
    {
        $this->assertTrue(config('meatpress.noindex_outside_production'));
        $this->assertTrue(config('meatpress.searchable_custom_fields.enabled'));
        $this->assertSame('acf-json', config('meatpress.acf_local_json.directory'));
        $this->assertSame(
            ['duracelltomi-google-tag-manager', 'ga-google-analytics'],
            config('meatpress.optional_plugins')
        );
    }

    public function test_commands_are_registered(): void
    {
        $commands = array_keys(Artisan::all());

        $this->assertContains('plugins:activate', $commands);
        $this->assertContains('salts:generate', $commands);
    }

    public function test_hooks_are_registered(): void
    {
        foreach (['wp_robots', 'save_post', 'init', 'wp_enqueue_scripts', 'acf/settings/save_json', 'acf/settings/load_json'] as $hook) {
            $this->assertTrue($this->hooks->has($hook), "Hook {$hook} is not registered");
        }

        $this->assertSame(100, $this->hooks->registered['wp_enqueue_scripts'][0]['priority']);
    }
}
