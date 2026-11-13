<?php

declare(strict_types=1);

namespace Meatpress\Media\Infrastructure;

use Meatpress\Media\Domain\Contracts\ThumbnailDriver;
use Meatpress\Media\Domain\Models\ImageSizeSpec;
use Meatpress\Media\Domain\Models\MediaConfig;
use Meatpress\Media\Domain\Services\SizedFileName;

/**
 * The default driver: a size is a file of the library, named `photo-card-600x400-{signature}.webp`,
 * made when the image is uploaded or the first time it is asked for (the fallback route).
 */
final readonly class MeatpressThumbnailDriver implements ThumbnailDriver
{
    public function __construct(
        private SizedFileName $names,
        private MediaConfig $config,
    ) {}

    public function url(string $originalKey, ImageSizeSpec $spec, int $originalWidth, int $originalHeight): string
    {
        return $this->config->url.'/'.ltrim($this->names->for($originalKey, $spec, $originalWidth, $originalHeight), '/');
    }

    public function makesFiles(): bool
    {
        return true;
    }

    public function sizeOf(string $url): ?string
    {
        $prefix = $this->config->url.'/';

        if (! str_starts_with($url, $prefix)) {
            return null;
        }

        return $this->names->parse(substr($url, strlen($prefix)))['name'] ?? null;
    }

    public function refusal(ImageSizeSpec $spec): ?string
    {
        return null;
    }
}
