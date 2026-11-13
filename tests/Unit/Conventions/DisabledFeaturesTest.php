<?php

declare(strict_types=1);

namespace Tests\Unit\Conventions;

final class DisabledFeaturesTest extends ConventionsTestCase
{
    protected function defineEnvironment($app): void
    {
        $app['config']->set('meatpress.noindex_outside_production', false);
        $app['config']->set('meatpress.searchable_custom_fields.enabled', false);
        $app['config']->set('meatpress.acf_local_json.enabled', false);
    }

    public function test_disabled_features_register_no_hook(): void
    {
        $this->assertFalse($this->hooks->has('wp_robots'));
        $this->assertFalse($this->hooks->has('save_post'));
        $this->assertFalse($this->hooks->has('acf/settings/save_json'));
    }
}
