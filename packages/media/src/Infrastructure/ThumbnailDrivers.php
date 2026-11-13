<?php

declare(strict_types=1);

namespace Meatpress\Media\Infrastructure;

use InvalidArgumentException;
use Meatpress\Media\Application\Services\ImageSizes;
use Meatpress\Media\Domain\Contracts\ThumbnailDriver;
use Meatpress\Media\Domain\Models\MediaConfig;
use Meatpress\Media\Domain\Models\ThumbnailDriverSettings;
use Meatpress\Media\Domain\Services\ImgproxySigner;

/**
 * Picks the driver of the sizes from the settings. A driver that is badly set up does not take the
 * site down: it is reported, and the module goes on with the default driver, which needs nothing.
 */
final class ThumbnailDrivers
{
    /**
     * @param  callable(string): void|null  $report  Receives why the default driver was kept; `error_log` by default
     */
    public static function make(ThumbnailDriverSettings $settings, ImageSizes $sizes, MediaConfig $config, ?callable $report = null): ThumbnailDriver
    {
        $report ??= static function (string $message): void {
            \error_log('meatpress/media: '.$message);
        };

        try {
            return match ($settings->driver) {
                ThumbnailDriverSettings::MEATPRESS => new MeatpressThumbnailDriver($sizes->names(), $config),
                ThumbnailDriverSettings::BUNNY => new BunnyThumbnailDriver($config, $sizes),
                ThumbnailDriverSettings::IMGPROXY => self::imgproxy($settings, $config),
                default => throw new InvalidArgumentException(sprintf('The sizes driver "%s" is not known; use "meatpress", "bunny" or "imgproxy".', $settings->driver)),
            };
        } catch (InvalidArgumentException $exception) {
            $report($exception->getMessage().' The default driver is used.');

            return new MeatpressThumbnailDriver($sizes->names(), $config);
        }
    }

    private static function imgproxy(ThumbnailDriverSettings $settings, MediaConfig $config): ImgproxyThumbnailDriver
    {
        if ($settings->imgproxyUrl === null || $settings->imgproxyKey === null || $settings->imgproxySalt === null) {
            throw new InvalidArgumentException('The imgproxy driver needs MEATPRESS_MEDIA_IMGPROXY_URL, _KEY and _SALT.');
        }

        if ($settings->imgproxySource === 's3' && $settings->imgproxyBucket === null) {
            throw new InvalidArgumentException('The imgproxy source "s3" needs the bucket of the library (MEATPRESS_MEDIA_BUCKET).');
        }

        return new ImgproxyThumbnailDriver(
            new ImgproxySigner($settings->imgproxyKey, $settings->imgproxySalt, $settings->imgproxySignatureSize),
            $config,
            $settings->imgproxyUrl,
            $settings->imgproxySource === 's3' ? $settings->imgproxyBucket : null,
        );
    }
}
