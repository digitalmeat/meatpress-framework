<?php

declare(strict_types=1);

namespace Meatpress\BlockCategory\Application\Services;

use Meatpress\BlockCategory\Domain\Contracts\BlockCategoryRegistrarInterface;
use Meatpress\BlockCategory\Domain\Contracts\BlockCategoryServiceInterface;
use Meatpress\BlockCategory\Domain\Models\BlockCategory;
use Meatpress\Collection\Domain\Contracts\CollectionFactoryInterface;
use Meatpress\Config\Domain\Contracts\ConfigRepositoryInterface;

/**
 * Application service for block category use cases.
 *
 * This service orchestrates the domain logic and coordinates
 * between the configuration, collection, and registration infrastructure.
 */
class BlockCategoryService implements BlockCategoryServiceInterface
{
    /**
     * Create a new block category service instance.
     */
    public function __construct(
        private readonly ConfigRepositoryInterface $config,
        private readonly CollectionFactoryInterface $collectionFactory,
        private readonly BlockCategoryRegistrarInterface $registrar
    ) {}

    /**
     * {@inheritdoc}
     */
    public function registerConfiguredCategories(): void
    {
        $configData = $this->config->get('theme.gutenberg.categories.blocks', []);

        if (empty($configData)) {
            return;
        }

        $configuredCategories = $this->collectionFactory->make($configData);

        if ($configuredCategories->isEmpty()) {
            return;
        }

        // Transform data into proper format for registration
        $formattedCategories = $configuredCategories->map(function (array $args, string $slug): array {
            $blockCategory = new BlockCategory(
                $slug,
                $args['label'] ?? $args['title'] ?? ''
            );

            return $blockCategory->toArray();
        });

        $this->registrar->registerCategories($formattedCategories);
    }
}
