<?php

declare(strict_types=1);

namespace Meatpress\Theme\Infrastructure\Services;

use Meatpress\Theme\Domain\Contracts\ContentUrlResolverInterface;

/**
 * Answers the content URL through WordPress's content_url().
 */
class WordPressContentUrlResolver implements ContentUrlResolverInterface
{
    public function contentUrl(string $path = ''): string
    {
        return \content_url($path);
    }
}
