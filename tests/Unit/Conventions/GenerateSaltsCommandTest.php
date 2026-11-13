<?php

declare(strict_types=1);

namespace Tests\Unit\Conventions;

final class GenerateSaltsCommandTest extends ConventionsTestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->directory = sys_get_temp_dir().'/meatpress-'.bin2hex(random_bytes(4));
        mkdir($this->directory);
        file_put_contents($this->directory.'/.env', "APP_NAME=Test\nAUTH_KEY=\n");

        $this->app->useEnvironmentPath($this->directory);
    }

    protected function tearDown(): void
    {
        @unlink($this->directory.'/.env');
        @rmdir($this->directory);

        parent::tearDown();
    }

    public function test_it_writes_every_salt(): void
    {
        $this->artisan('salts:generate')->assertSuccessful();

        $env = $this->env();

        foreach (['AUTH_KEY', 'SECURE_AUTH_KEY', 'LOGGED_IN_KEY', 'NONCE_KEY', 'AUTH_SALT', 'SECURE_AUTH_SALT', 'LOGGED_IN_SALT', 'NONCE_SALT'] as $key) {
            $this->assertMatchesRegularExpression("/^{$key}=[A-Za-z0-9]{64}$/m", $env);
        }

        $this->assertStringContainsString('APP_NAME=Test', $env);
        $this->assertSame(1, preg_match_all('/^AUTH_KEY=/m', $env), 'AUTH_KEY must be replaced, not duplicated');
    }

    public function test_it_keeps_existing_salts(): void
    {
        $this->artisan('salts:generate')->assertSuccessful();
        $first = $this->env();

        $this->artisan('salts:generate')->assertSuccessful();

        $this->assertSame($first, $this->env());
    }

    public function test_force_replaces_existing_salts(): void
    {
        $this->artisan('salts:generate')->assertSuccessful();
        $first = $this->env();

        $this->artisan('salts:generate', ['--force' => true])->assertSuccessful();

        $this->assertNotSame($first, $this->env());
    }

    private function env(): string
    {
        return (string) file_get_contents($this->directory.'/.env');
    }
}
