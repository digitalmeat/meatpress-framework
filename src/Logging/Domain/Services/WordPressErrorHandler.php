<?php

declare(strict_types=1);

namespace Meatpress\Logging\Domain\Services;

use Meatpress\Logging\Domain\Contracts\WordPressErrorLoggerInterface;
use Meatpress\Logging\Domain\Models\WordPressError;

class WordPressErrorHandler
{
    public function __construct(
        private readonly WordPressErrorLoggerInterface $logger
    ) {}

    public function handleDoingItWrong(
        string $function,
        string $message,
        string $version,
        array $context = []
    ): void {
        $error = WordPressError::doingItWrong($function, $message, $version, $context);
        $this->logger->logError($error);
    }

    public function handleDeprecatedFunction(
        string $function,
        string $replacement,
        string $version,
        array $context = []
    ): void {
        $error = WordPressError::deprecatedFunction($function, $replacement, $version, $context);
        $this->logger->logError($error);
    }

    public function handleDeprecatedArgument(
        string $function,
        string $message,
        string $version,
        array $context = []
    ): void {
        $error = WordPressError::deprecatedArgument($function, $message, $version, $context);
        $this->logger->logError($error);
    }

    public function disableTriggerError(): bool
    {
        return false;
    }
}
