<?php

declare(strict_types=1);

namespace Meatpress\Media\Domain\Events;

/**
 * A media changed visibility, relayed from the `meatpress/media/visibility_changed` action:
 * to audit the change, or to purge a CDN.
 */
final readonly class MediaVisibilityChanged
{
    public function __construct(
        public int $attachmentId,
        public bool $private,
    ) {}
}
