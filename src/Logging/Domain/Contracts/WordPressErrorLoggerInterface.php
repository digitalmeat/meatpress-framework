<?php

declare(strict_types=1);

namespace Meatpress\Logging\Domain\Contracts;

use Meatpress\Logging\Domain\Models\WordPressError;

interface WordPressErrorLoggerInterface
{
    public function logError(WordPressError $error): void;
}
