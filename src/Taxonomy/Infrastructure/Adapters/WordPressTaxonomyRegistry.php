<?php

declare(strict_types=1);

namespace Meatpress\Taxonomy\Infrastructure\Adapters;

use Meatpress\Taxonomy\Domain\Contracts\TaxonomyRegistryInterface;

/**
 * WordPress implementation of the TaxonomyRegistry interface.
 */
class WordPressTaxonomyRegistry implements TaxonomyRegistryInterface
{
    /**
     * Register a taxonomy with WordPress.
     *
     * @param  object  $taxonomy  The taxonomy to register
     * @return bool True if registration was successful
     */
    public function register(object $taxonomy): bool
    {
        if (! function_exists('\register_taxonomy')) {
            return false;
        }

        $args = $this->prepareTaxonomyArgs($taxonomy);
        $slug = method_exists($taxonomy, 'getSlug') ? $taxonomy->getSlug() : '';
        $objectType = method_exists($taxonomy, 'getObjectType') ? $taxonomy->getObjectType() : 'post';

        if (empty($slug)) {
            return false;
        }

        $result = \register_taxonomy($slug, $objectType, $args);

        return ! \is_wp_error($result);
    }

    /**
     * Check if a taxonomy exists in WordPress.
     *
     * @param  string  $slug  The taxonomy slug to check
     * @return bool True if the taxonomy exists
     */
    public function exists(string $slug): bool
    {
        if (function_exists('\taxonomy_exists')) {
            return \taxonomy_exists($slug);
        }

        return false;
    }

    /**
     * Get all registered taxonomies from WordPress.
     *
     * @return array<int|string, \WP_Taxonomy> The registered taxonomies
     */
    public function getAll(): array
    {
        if (function_exists('\get_taxonomies')) {
            return \get_taxonomies(['_builtin' => false], 'objects');
        }

        return [];
    }

    /**
     * Prepare taxonomy arguments for WordPress registration.
     *
     * @param  object  $taxonomy  The taxonomy object
     * @return array<string, mixed> The prepared arguments
     */
    private function prepareTaxonomyArgs(object $taxonomy): array
    {
        // Use the methods if they exist
        $args = method_exists($taxonomy, 'getArgs') ? $taxonomy->getArgs() : [];

        // Add labels if not explicitly provided
        if (! isset($args['labels'])) {
            $singular = method_exists($taxonomy, 'getSingularName') ? $taxonomy->getSingularName() : '';
            $plural = method_exists($taxonomy, 'getPluralName') ? $taxonomy->getPluralName() : '';

            $args['labels'] = $this->generateLabels(
                $singular ?? '',
                $plural ?? ''
            );
        }

        return $args;
    }

    /**
     * Generate labels for the taxonomy.
     *
     * @param  string  $singular  The singular name
     * @param  string  $plural  The plural name
     * @return array<string, string> The generated labels
     */
    private function generateLabels(string $singular, string $plural): array
    {
        return [
            'name' => $plural,
            'singular_name' => $singular,
            'menu_name' => $plural,
            'all_items' => sprintf(__('All %s', 'meatpress'), $plural),
            'edit_item' => sprintf(__('Edit %s', 'meatpress'), $singular),
            'view_item' => sprintf(__('View %s', 'meatpress'), $singular),
            'update_item' => sprintf(__('Update %s', 'meatpress'), $singular),
            'add_new_item' => sprintf(__('Add New %s', 'meatpress'), $singular),
            'new_item_name' => sprintf(__('New %s Name', 'meatpress'), $singular),
            'search_items' => sprintf(__('Search %s', 'meatpress'), $plural),
            'popular_items' => sprintf(__('Popular %s', 'meatpress'), $plural),
            'separate_items_with_commas' => sprintf(__('Separate %s with commas', 'meatpress'), $plural),
            'add_or_remove_items' => sprintf(__('Add or remove %s', 'meatpress'), $plural),
            'choose_from_most_used' => sprintf(__('Choose from the most used %s', 'meatpress'), $plural),
            'not_found' => sprintf(__('No %s found', 'meatpress'), $plural),
            'parent_item' => sprintf(__('Parent %s', 'meatpress'), $singular),
            'parent_item_colon' => sprintf(__('Parent %s:', 'meatpress'), $singular),
        ];
    }
}
