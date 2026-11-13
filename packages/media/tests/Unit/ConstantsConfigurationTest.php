<?php

declare(strict_types=1);

use Brain\Monkey\Filters;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Meatpress\Media\Infrastructure\WordPress\ConstantsConfiguration;
use Meatpress\Media\Tests\Fixtures\TemporaryStorage;

/**
 * @param  array<string, mixed>  $constants
 */
function constants(array $constants): ConstantsConfiguration
{
    return new ConstantsConfiguration(static fn (string $name): mixed => $constants[$name] ?? null);
}

it('falls back to the folder and the URL WordPress uses, so nothing changes until a setting is made', function (): void {
    $configuration = constants(['WP_CONTENT_DIR' => '/var/www/public/content', 'WP_CONTENT_URL' => 'https://site.test/content']);

    expect($configuration->config()->url)->toBe('https://site.test/content/uploads')
        ->and($configuration->config()->legacyUrls)->toBe([])
        ->and($configuration->localRoot())->toBe('/var/www/public/content/uploads');
});

it('reads the MEATPRESS_MEDIA constants into the configuration', function (): void {
    $config = constants([
        'MEATPRESS_MEDIA_URL' => 'https://media.client.fr/',
        'MEATPRESS_MEDIA_LEGACY_URLS' => ['https://old.test/content/uploads', 'https://older.test/uploads/'],
        'MEATPRESS_MEDIA_CACHE_CONTROL' => 'public, max-age=60',
        'MEATPRESS_MEDIA_PRIVATE_EXPIRY' => '+1 hour',
        'MEATPRESS_MEDIA_ASYNC_THUMBNAILS' => true,
    ])->config();

    expect($config->url)->toBe('https://media.client.fr')
        ->and($config->legacyUrls)->toBe(['https://old.test/content/uploads', 'https://older.test/uploads'])
        ->and($config->cacheControl)->toBe('public, max-age=60')
        ->and($config->privateExpiry)->toBe('+1 hour')
        ->and($config->asyncThumbnails)->toBeTrue();
});

it('accepts the legacy URLs as a comma separated string', function (): void {
    $config = constants(['MEATPRESS_MEDIA_URL' => 'https://m.test', 'MEATPRESS_MEDIA_LEGACY_URLS' => 'https://a.test/u, https://b.test/u'])->config();

    expect($config->legacyUrls)->toBe(['https://a.test/u', 'https://b.test/u']);
});

it('builds a local filesystem from MEATPRESS_MEDIA_ROOT', function (): void {
    $disk = new TemporaryStorage;
    $filesystem = constants(['MEATPRESS_MEDIA_DRIVER' => 'local', 'MEATPRESS_MEDIA_ROOT' => $disk->root])->filesystem();
    $filesystem->write('a/b.txt', 'x');

    expect(file_get_contents($disk->root.'/a/b.txt'))->toBe('x');
});

it('hands the filesystem to a filter first, for a service the constants cannot describe', function (): void {
    $custom = new Filesystem(new LocalFilesystemAdapter(sys_get_temp_dir()));
    Filters\expectApplied('meatpress/media/filesystem')->once()->with(null)->andReturn($custom);

    expect(constants(['MEATPRESS_MEDIA_DRIVER' => 'sftp'])->filesystem())->toBe($custom);
});

it('says what to install when the S3 driver is asked for and its SDK is not there', function (): void {
    $configuration = constants(['MEATPRESS_MEDIA_DRIVER' => 's3', 'MEATPRESS_MEDIA_BUCKET' => 'media', 'MEATPRESS_MEDIA_REGION' => 'eu-west-3']);

    expect(fn () => $configuration->filesystem())->toThrow(RuntimeException::class, 'league/flysystem-aws-s3-v3');
})->skip(fn () => class_exists('League\Flysystem\AwsS3V3\AwsS3V3Adapter'), 'the S3 SDK is installed');

it('refuses a driver it does not know, and an S3 disk without a bucket', function (): void {
    expect(fn () => constants(['MEATPRESS_MEDIA_DRIVER' => 'ftp'])->filesystem())->toThrow(RuntimeException::class, 'meatpress/media/filesystem')
        ->and(fn () => constants(['MEATPRESS_MEDIA_DRIVER' => 's3'])->filesystem())->toThrow(RuntimeException::class, 'MEATPRESS_MEDIA_BUCKET');
});

it('reads the limits of a size made on demand, with the defaults of the specification', function (): void {
    $default = constants([])->thumbnailLimits();
    $set = constants(['MEATPRESS_MEDIA_THUMBNAILS_MAX_PIXELS' => 10_000_000, 'MEATPRESS_MEDIA_THUMBNAILS_CONCURRENCY' => 2, 'MEATPRESS_MEDIA_THUMBNAILS_WAIT' => 3])->thumbnailLimits();

    expect([$default->maxPixels, $default->maxBytes, $default->concurrency, $default->wait])->toBe([50_000_000, 31_457_280, 4, 10])
        ->and([$set->maxPixels, $set->concurrency, $set->wait])->toBe([10_000_000, 2, 3]);
});

it('keeps the default driver for sizes until a constant says otherwise', function (): void {
    $settings = constants([])->thumbnailDriver();

    expect($settings->driver)->toBe('meatpress')->and($settings->imgproxySignatureSize)->toBe(32)->and($settings->imgproxySource)->toBe('url');
});

it('reads the driver of the sizes and its settings from the constants', function (): void {
    $settings = constants([
        'MEATPRESS_MEDIA_THUMBNAILS_DRIVER' => 'imgproxy',
        'MEATPRESS_MEDIA_IMGPROXY_URL' => 'https://img.client.fr/',
        'MEATPRESS_MEDIA_IMGPROXY_KEY' => '736563726574',
        'MEATPRESS_MEDIA_IMGPROXY_SALT' => '68656C6C6F',
        'MEATPRESS_MEDIA_IMGPROXY_SIGNATURE_SIZE' => '16',
        'MEATPRESS_MEDIA_IMGPROXY_SOURCE' => 's3',
        'MEATPRESS_MEDIA_BUCKET' => 'client-media',
        'MEATPRESS_MEDIA_BUNNY_PULL_ZONE' => 1234,
        'MEATPRESS_MEDIA_BUNNY_API_KEY' => 'access-key',
    ])->thumbnailDriver();

    expect($settings->driver)->toBe('imgproxy')
        ->and($settings->imgproxyUrl)->toBe('https://img.client.fr')
        ->and($settings->imgproxyKey)->toBe('736563726574')
        ->and($settings->imgproxySalt)->toBe('68656C6C6F')
        ->and($settings->imgproxySignatureSize)->toBe(16)
        ->and($settings->imgproxySource)->toBe('s3')
        ->and($settings->imgproxyBucket)->toBe('client-media')
        ->and($settings->bunnyPullZone)->toBe('1234')
        ->and($settings->bunnyApiKey)->toBe('access-key');
});

it('does not keep the bucket of the library for the sizes unless the source is s3', function (): void {
    $settings = constants(['MEATPRESS_MEDIA_BUCKET' => 'client-media', 'MEATPRESS_MEDIA_IMGPROXY_URL' => 'https://img.client.fr'])->thumbnailDriver();

    expect($settings->imgproxyBucket)->toBeNull();
});
