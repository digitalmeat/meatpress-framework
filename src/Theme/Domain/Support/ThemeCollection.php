<?php

declare(strict_types=1);

namespace Meatpress\Theme\Domain\Support;

use Meatpress\Collection\Domain\Contracts\CollectionFactoryInterface;
use Meatpress\Collection\Domain\Contracts\CollectionInterface;
use Meatpress\Theme\Domain\Exceptions\ThemeSetupException;

/**
 * Theme collection utility class.
 *
 * Provides a clean, object-oriented interface for creating and working
 * with collections while maintaining hexagonal architecture principles.
 */
class ThemeCollection
{
    private static ?CollectionFactoryInterface $collectionFactory = null;

    /**
     * Set the collection factory.
     */
    public static function setFactory(CollectionFactoryInterface $factory): void
    {
        self::$collectionFactory = $factory;
    }

    /**
     * Create a collection from the given items.
     *
     * @param  array  $items  Items to collect
     * @return CollectionInterface Framework-agnostic collection
     *
     * @throws ThemeSetupException If the collection factory is not set
     */
    public static function make(array $items = []): CollectionInterface
    {
        if (! self::$collectionFactory instanceof CollectionFactoryInterface) {
            throw ThemeSetupException::collectionFactoryMissing();
        }

        return self::$collectionFactory->make($items);
    }
}
