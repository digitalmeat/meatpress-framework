<?php

declare(strict_types=1);

namespace Meatpress\Collection\Infrastructure\Services;

use Meatpress\Collection\Domain\Contracts\CollectionFactoryInterface;
use Meatpress\Collection\Domain\Contracts\CollectionInterface;
use Meatpress\Collection\Infrastructure\Adapters\LaravelCollectionAdapter;

/**
 * Laravel implementation of the CollectionFactoryInterface.
 */
class LaravelCollectionFactory implements CollectionFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function make(array $items): CollectionInterface
    {
        return new LaravelCollectionAdapter($items);
    }
}
