<?php

declare(strict_types=1);

use Meatpress\Hook\Domain\Contract\Filter;
use Meatpress\Login\Domain\Contracts\DesignSettingsRepository;
use Meatpress\Login\Domain\Models\LoginConfiguration;
use Meatpress\Login\Domain\Models\LoginStylesheet;
use Meatpress\Login\Domain\Models\ResolvedLogo;
use Meatpress\Login\Infrastructure\Services\LoginScreen;
use Meatpress\Login\Infrastructure\Services\LogoResolver;

/**
 * What the login screen actually prints, and what it refuses to print.
 */
function emptySettings(): DesignSettingsRepository
{
    return new class implements DesignSettingsRepository
    {
        public function colors(): array
        {
            return [];
        }

        public function radii(): array
        {
            return [];
        }

        public function fontFamilies(): array
        {
            return [];
        }
    };
}

function passthroughFilter(): Filter
{
    $filter = Mockery::mock(Filter::class);
    $filter->shouldReceive('apply')->andReturnUsing(fn (string $hook, mixed $value): mixed => $value);

    return $filter;
}

function screenFor(array $config, ?Filter $filter = null, string $rules = '.meatpress-login form {}'): LoginScreen
{
    return new LoginScreen(
        LoginConfiguration::fromArray($config),
        emptySettings(),
        new LogoResolver(sys_get_temp_dir()),
        new LoginStylesheet($rules),
        $filter ?? passthroughFilter(),
    );
}

/*
 * The screen is a WordPress adapter: it runs on login_head, where WordPress
 * is fully loaded. These are the functions it reaches for, stubbed to the
 * answers a site that has configured nothing would give.
 */
beforeEach(function (): void {
    Brain\Monkey\Functions\when('get_theme_mod')->justReturn(false);
    Brain\Monkey\Functions\when('get_bloginfo')->justReturn('');
    Brain\Monkey\Functions\when('home_url')->justReturn('https://example.test/');
});

describe('LoginScreen', function (): void {
    it('marks the screen so the stylesheet can be scoped to it', function (): void {
        expect(screenFor([])->bodyClass(['login', 'wp-core-ui']))
            ->toBe(['login', 'wp-core-ui', 'meatpress-login']);
    });

    it('leaves body classes alone when WordPress hands it something else', function (): void {
        // login_body_class is a filter, and a plugin ahead of us can have
        // replaced its value with anything.
        expect(screenFor([])->bodyClass('login'))->toBe('login');
    });

    it('prints the stylesheet into the head', function (): void {
        ob_start();
        screenFor([])->printStyles();
        $output = ob_get_clean();

        expect($output)->toStartWith('<style id="meatpress-login">')
            ->and($output)->toContain('--meatpress-login-primary:')
            ->and(trim($output))->toEndWith('</style>');
    });

    it('prints no empty style element', function (): void {
        $filter = Mockery::mock(Filter::class);
        $filter->shouldReceive('apply')->andReturnUsing(
            fn (string $hook, mixed $value): mixed => $hook === 'meatpress/login/styles' ? '' : $value
        );

        ob_start();
        screenFor([], $filter)->printStyles();

        expect(ob_get_clean())->toBe('');
    });

    it('builds the stylesheet once per request', function (): void {
        $screen = screenFor([]);

        expect($screen->css())->toBe($screen->css());
    });

    it('lets a filter replace the palette', function (): void {
        $filter = Mockery::mock(Filter::class);
        $filter->shouldReceive('apply')->andReturnUsing(
            fn (string $hook, mixed $value): mixed => $hook === 'meatpress/login/palette'
                ? ['primary' => '#abcdef', 'bad' => 12]
                : $value
        );

        expect(screenFor([], $filter)->css())->toContain('--meatpress-login-primary: #abcdef;');
    });

    it('ignores a filter that hands back something that is not a palette', function (): void {
        $filter = Mockery::mock(Filter::class);
        $filter->shouldReceive('apply')->andReturnUsing(
            fn (string $hook, mixed $value): mixed => $hook === 'meatpress/login/palette' ? 'nope' : $value
        );

        expect(screenFor([], $filter)->css())->toContain('--meatpress-login-primary:');
    });

    it('lets a filter supply a logo the theme never named', function (): void {
        $filter = Mockery::mock(Filter::class);
        $filter->shouldReceive('apply')->andReturnUsing(
            fn (string $hook, mixed $value): mixed => $hook === 'meatpress/login/logo'
                ? new ResolvedLogo('url("https://example.test/x.svg")', 200, 80)
                : $value
        );

        expect(screenFor([], $filter)->css())->toContain('--meatpress-login-logo: url("https://example.test/x.svg");');
    });

    it('sends the logo to the site it belongs to, not to wordpress.org', function (): void {
        Brain\Monkey\Functions\when('home_url')->justReturn('https://example.test/');

        expect(screenFor([])->headerUrl())->toBe('https://example.test/');
    });

    it('obeys a destination the theme stated', function (): void {
        expect(screenFor(['logo' => ['source' => 'a.svg', 'url' => 'https://custom.test/']])
            ->headerUrl())->toBe('https://custom.test/');
    });

    it('names the site behind the logo', function (): void {
        Brain\Monkey\Functions\when('get_bloginfo')->justReturn('Example');

        expect(screenFor([])->headerText('Powered by WordPress'))->toBe('Example');
    });

    it("keeps WordPress's words when the site has no name", function (): void {
        Brain\Monkey\Functions\when('get_bloginfo')->justReturn('   ');

        expect(screenFor([])->headerText('Powered by WordPress'))->toBe('Powered by WordPress');
    });

    it('obeys text the theme stated', function (): void {
        expect(screenFor(['logo' => ['source' => 'a.svg', 'text' => 'Example Co']])
            ->headerText('Powered by WordPress'))->toBe('Example Co');
    });

    it('mentions the framework in the footer', function (): void {
        ob_start();
        screenFor([])->printCredit();

        expect(ob_get_clean())
            ->toContain('class="meatpress-login-credit"')
            ->toContain('https://github.com/digitalmeat/meatpress-framework/tree/main/docs')
            ->toContain('rel="noopener noreferrer"');
    });

    it('says nothing when the site asked it not to', function (): void {
        ob_start();
        screenFor(['powered_by' => false])->printCredit();

        expect(ob_get_clean())->toBe('');
    });

    it('lets a filter replace or remove the credit', function (): void {
        $filter = Mockery::mock(Filter::class);
        $filter->shouldReceive('apply')->andReturnUsing(
            fn (string $hook, mixed $value): mixed => $hook === 'meatpress/login/credit' ? null : $value
        );

        ob_start();
        screenFor([], $filter)->printCredit();

        expect(ob_get_clean())->toBe('');
    });

    it('draws the logo the theme named', function (): void {
        expect(screenFor(['logo' => ['source' => 'https://example.test/brand.svg', 'width' => 300, 'height' => 100]])->css())
            ->toContain('--meatpress-login-logo: url("https://example.test/brand.svg");')
            ->toContain('--meatpress-login-logo-width: 300px;');
    });

    it('falls back to the logo the site already set in the customiser', function (): void {
        // A site that never opens config/login.php has still told WordPress
        // what its mark is.
        Brain\Monkey\Functions\when('get_theme_mod')->justReturn(7);
        Brain\Monkey\Functions\when('wp_get_attachment_image_src')
            ->justReturn(['https://example.test/uploads/mark.png', 400, 100, false]);

        expect(screenFor([])->css())->toContain('--meatpress-login-logo: url("https://example.test/uploads/mark.png");');
    });

    it('draws no logo when there is none to draw', function (mixed $themeMod): void {
        Brain\Monkey\Functions\when('get_theme_mod')->justReturn($themeMod);

        expect(screenFor([])->css())->not->toContain('--meatpress-login-logo');
    })->with([
        'never set' => [false],
        'set to nothing' => [0],
        'set to nonsense' => ['none'],
    ]);
});
