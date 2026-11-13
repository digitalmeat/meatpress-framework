<?php

declare(strict_types=1);

namespace Meatpress\Route\Infrastructure\Services\Contracts;

use Meatpress\Route\Domain\Contracts\WordPressTypeResolverInterface as DomainWordPressTypeResolverInterface;

/*
 * The interface moved to the domain layer, where the application layer may
 * depend on it. The former name keeps working.
 */
class_alias(DomainWordPressTypeResolverInterface::class, 'Meatpress\\Route\\Infrastructure\\Services\\Contracts\\WordPressTypeResolverInterface');
