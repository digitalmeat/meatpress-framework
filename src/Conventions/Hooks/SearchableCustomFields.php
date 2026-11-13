<?php

declare(strict_types=1);

namespace Meatpress\Conventions\Hooks;

use WP_Post;

/**
 * Copies the text stored in custom fields (ACF) into post_content on save.
 *
 * Pages built with custom fields keep post_content empty, so the native
 * WordPress search finds nothing in them. Concatenating the text-like meta
 * values into post_content makes that content searchable.
 */
final class SearchableCustomFields
{
    /**
     * @param  list<string>  $metaKeyPatterns  SQL LIKE patterns of the meta keys to index
     * @param  list<string>  $ignoredPostTypes
     */
    public function __construct(
        private readonly array $metaKeyPatterns,
        private readonly array $ignoredPostTypes,
    ) {}

    public function syncIntoContent(int $postId, WP_Post $post): void
    {
        if ($this->metaKeyPatterns === []
            || wp_is_post_revision($postId)
            || wp_is_post_autosave($postId)
            || in_array($post->post_type, $this->ignoredPostTypes, true)) {
            return;
        }

        $values = array_filter($this->metaValues($postId));

        // Without field values there is nothing to index: keep the post's own content.
        if ($values === []) {
            return;
        }

        global $wpdb;

        // Direct update: wp_update_post() would trigger save_post again.
        $wpdb->update(
            $wpdb->posts,
            ['post_content' => implode('. ', $values)],
            ['ID' => $postId],
            ['%s'],
            ['%d']
        );

        clean_post_cache($postId);
    }

    /**
     * @return list<string>
     */
    private function metaValues(int $postId): array
    {
        global $wpdb;

        $keyConditions = implode(' OR ', array_fill(0, count($this->metaKeyPatterns), 'meta_key LIKE %s'));

        // ACF stores a "_<field>" reference next to every value: skip those keys and
        // any value that is itself a field key.
        $sql = "SELECT meta_value FROM {$wpdb->postmeta}
                WHERE post_id = %d
                  AND ({$keyConditions})
                  AND meta_key NOT LIKE %s
                  AND meta_value NOT LIKE %s";

        return $wpdb->get_col($wpdb->prepare($sql, $postId, ...$this->metaKeyPatterns, ...['\_%', 'field\_%']));
    }
}
