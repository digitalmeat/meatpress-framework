<?php

declare(strict_types=1);

namespace Meatpress\Logging\Domain\Contracts;

interface WordPressErrorHookRegistrarInterface
{
    public function registerErrorHandlers(): void;
}
