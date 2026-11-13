<?php

declare(strict_types=1);

use Brain\Monkey\Functions;
use Meatpress\Asset\Infrastructure\Services\AssetFile;
use Meatpress\Theme\Domain\Contracts\ContentUrlResolverInterface;
use Meatpress\Theme\Domain\Contracts\ThemeAssetUrlResolverInterface;
use Meatpress\Theme\Domain\Contracts\ThemeFeatureRegistrarInterface;
use Meatpress\Theme\Infrastructure\Services\ThemeAssetFileResolver;
use Meatpress\Theme\Infrastructure\Services\WordPressContentUrlResolver;
use Meatpress\Theme\Infrastructure\Services\WordPressThemeFeatureRegistrar;

/**
 * The adapters behind the theme domain's ports: each one is the WordPress (or
 * asset build) call the domain used to make itself.
 */
describe('WordPressThemeFeatureRegistrar', function (): void {
    it('implements the domain port', function (): void {
        expect(new WordPressThemeFeatureRegistrar)->toBeInstanceOf(ThemeFeatureRegistrarInterface::class);
    });

    it('registers menu locations with register_nav_menus()', function (): void {
        Functions\expect('register_nav_menus')->once()->with(['primary' => 'Primary menu']);

        (new WordPressThemeFeatureRegistrar)->registerNavMenus(['primary' => 'Primary menu']);
    });

    it('registers a sidebar with register_sidebar()', function (): void {
        Functions\expect('register_sidebar')->once()->with(['id' => 'primary', 'name' => 'Primary']);

        (new WordPressThemeFeatureRegistrar)->registerSidebar(['id' => 'primary', 'name' => 'Primary']);
    });

    it('registers an image size with add_image_size()', function (): void {
        Functions\expect('add_image_size')->once()->with('card', 640, 480, true);

        (new WordPressThemeFeatureRegistrar)->addImageSize('card', 640, 480, true);
    });
});

describe('WordPressContentUrlResolver', function (): void {
    it('implements the domain port', function (): void {
        expect(new WordPressContentUrlResolver)->toBeInstanceOf(ContentUrlResolverInterface::class);
    });

    it('answers with content_url()', function (): void {
        Functions\expect('content_url')->once()->with('themes')->andReturn('https://example.test/content/themes');

        expect((new WordPressContentUrlResolver)->contentUrl('themes'))->toBe('https://example.test/content/themes');
    });
});

describe('ThemeAssetFileResolver', function (): void {
    afterEach(function (): void {
        AssetFile::resolveUsing(null);
    });

    it('implements the domain port', function (): void {
        expect(new ThemeAssetFileResolver)->toBeInstanceOf(ThemeAssetUrlResolverInterface::class);
    });

    it('resolves the file through the theme asset container', function (): void {
        $asked = [];
        AssetFile::resolveUsing(function (string $file, string $container) use (&$asked): string {
            $asked[] = [$file, $container];

            return 'https://example.test/build/theme/default/'.$file;
        });

        expect((new ThemeAssetFileResolver)->assetUrl('app.js'))->toBe('https://example.test/build/theme/default/app.js')
            ->and($asked)->toBe([['app.js', 'theme']]);
    });
});
