<?php

declare(strict_types=1);

namespace Meatpress\BlockBinding\Infrastructure\Adapters;

use Meatpress\BlockBinding\Domain\Contracts\ValuePresenterInterface;

/**
 * Escapes and words bound values with WordPress's functions.
 */
final readonly class WordPressValuePresenter implements ValuePresenterInterface
{
    public function html(string $value): string
    {
        return \esc_html($value);
    }

    public function richText(string $value): string
    {
        return \wp_kses_post($value);
    }

    public function url(string $value): string
    {
        return \sanitize_url($value);
    }

    public function boolean(bool $value, ?string $true = null, ?string $false = null): string
    {
        return $value ? ($true ?? \__('Yes')) : ($false ?? \__('No'));
    }
}
