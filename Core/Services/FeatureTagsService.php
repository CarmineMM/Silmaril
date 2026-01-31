<?php

namespace Silmaril\Core\Services;

use Silmaril\Core\Foundation\Service;

class FeatureTagsService extends Service
{
    public function init(): void
    {
        $disabledAllFeatures = $this->theme->config('theme.features.tags.disable_globally', false);

        if ($this->theme->config('theme.features.tags.disable_for_posts', false) || $disabledAllFeatures) {
            \add_action('init', [$this, 'unregisterPostTags'], 400);
        }

        if ($this->theme->config('theme.features.tags.remove_admin_menu', false) || $disabledAllFeatures) {
            \add_action('admin_menu', [$this, 'removeTagsMenu'], 401);
        }

        if ($this->theme->config('theme.features.tags.remove_admin_meta_box', false) || $disabledAllFeatures) {
            \add_action('admin_menu', [$this, 'removeTagsMetaBox'], 402);
        }

        if ($this->theme->config('theme.features.tags.remove_admin_columns', false) || $disabledAllFeatures) {
            \add_filter('manage_post_sortable_columns', [$this, 'removeTagsColumns'], 400);
        }

        if ($this->theme->config('theme.features.tags.remove_tag_feed', false) || $disabledAllFeatures) {
            \add_filter('tag_feed', '__return_false', 401);
        }

        if ($this->theme->config('theme.features.tags.remove_tag_widgets', false) || $disabledAllFeatures) {
            \add_action('widgets_init', [$this, 'unregisterTagWidgets'], 403);
        }
    }

    /**
     * Desregistrar taxonomía de tags para posts
     */
    public function unregisterPostTags(): void
    {
        // Remover la taxonomía 'post_tag' del post type 'post'
        \unregister_taxonomy_for_object_type('post_tag', 'post');
    }

    /**
     * Remover menú de tags del admin
     */
    public function removeTagsMenu(): void
    {
        // Remover submenú de tags bajo Posts
        \remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=post_tag');
    }

    /**
     * Remover meta box de tags
     */
    public function removeTagsMetaBox(): void
    {
        \remove_meta_box('tagsdiv-post_tag', 'post', 'side');
    }

    /**
     * Remover columnas de tags en admin
     */
    public function removeTagsColumns(array|string $columns): array|string
    {
        if (\is_array($columns)) {
            unset($columns['tags']);
        }

        return $columns;
    }

    /**
     * Desregistrar widgets de tags
     */
    public function unregisterTagWidgets(): void
    {
        \unregister_widget('WP_Widget_Tag_Cloud');
        \unregister_widget('WP_Widget_Meta'); // Widget Meta incluye enlaces a tags
    }
}
