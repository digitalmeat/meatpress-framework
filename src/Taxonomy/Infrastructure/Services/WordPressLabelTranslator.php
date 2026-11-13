<?php

declare(strict_types=1);

namespace Meatpress\Taxonomy\Infrastructure\Services;

use Meatpress\Taxonomy\Domain\Contracts\LabelTranslatorInterface;

/**
 * Translates taxonomy labels through `__()`, which Meatpress hands to WordPress's catalogues.
 */
class WordPressLabelTranslator implements LabelTranslatorInterface
{
    public function translate(string $text, string $domain): string
    {
        return __($text, $domain);
    }
}
