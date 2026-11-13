<?php

declare(strict_types=1);

namespace Meatpress\Media;

use League\Flysystem\FilesystemOperator;
use Meatpress\Hook\Adapter\Out\WordPress\Action;
use Meatpress\Media\Domain\Models\MediaConfig;
use Meatpress\Media\Infrastructure\Flysystem\FlysystemMediaStorage;
use Meatpress\Media\Infrastructure\WordPress\ConstantsConfiguration;
use Meatpress\Media\Infrastructure\WordPress\WordPressMedia;

/**
 * Puts the media library on a Flysystem disk for a WordPress that is not a
 * Meatpress application: a classic theme, a plugin, a Bedrock site. In Meatpress,
 * the framework does it for you from `config/media.php`.
 *
 * ```php
 * // wp-config.php, then an mu-plugin or the first plugin loaded
 * define('MEATPRESS_MEDIA_DRIVER', 's3');
 * define('MEATPRESS_MEDIA_BUCKET', 'client-media');
 * define('MEATPRESS_MEDIA_URL', 'https://media.client.fr');
 *
 * \Meatpress\Media\Media::register();
 * ```
 */
final class Media
{
    /**
     * Registers the `meatpress://` wrapper and the hooks. Call it before WordPress builds its upload directory:
     * from a must-use plugin, or the first plugin loaded.
     */
    public static function register(?FilesystemOperator $filesystem = null, ?MediaConfig $config = null): WordPressMedia
    {
        $constants = new ConstantsConfiguration;

        $media = new WordPressMedia(
            new FlysystemMediaStorage($filesystem ?? $constants->filesystem()),
            $config ?? $constants->config(),
            new Action,
            limits: $constants->thumbnailLimits(),
            thumbnails: $constants->thumbnailDriver(),
        );
        $media->register();

        return $media;
    }
}
