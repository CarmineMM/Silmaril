<?php

namespace Silmaril\Core\Services;

use Silmaril\Core\Foundation\Service;

class FeatureCategoriesService extends Service
{
    public function init(): void
    {
        $disabledAllFeatures = $this->theme->config('theme.features.categories.disable_globally', false);

        if ($this->theme->config('theme.features.categories.disable_for_posts', false) || $disabledAllFeatures) {
            \add_action('init', [$this, 'unregisterPostCategories']);
        }

        if ($this->theme->config('theme.features.categories.remove_admin_menu', false) || $disabledAllFeatures) {
            \add_action('admin_menu', [$this, 'removeCategoriesMenu']);
        }

        if ($this->theme->config('theme.features.categories.remove_admin_meta_box', false) || $disabledAllFeatures) {
            \add_action('admin_menu', [$this, 'removeCategoriesMetaBox']);
        }

        if ($this->theme->config('theme.features.categories.remove_admin_columns', false) || $disabledAllFeatures) {
            \add_action('admin_menu', [$this, 'removeCategoriesColumns']);
        }

        if ($this->theme->config('theme.features.categories.remove_category_feed', false) || $disabledAllFeatures) {
            \add_filter('category_feed', '__return_false');
        }

        if ($this->theme->config('theme.features.categories.remove_category_widgets', false) || $disabledAllFeatures) {
            \add_action('widgets_init', [$this, 'unregisterCategoryWidgets']);
        }
    }

    /**
     * Desregistrar taxonomía de categorías para posts
     */
    public function unregisterPostCategories(): void
    {
        // Remover la taxonomía 'category' del post type 'post'
        \unregister_taxonomy_for_object_type('category', 'post');
    }

    /**
     * Remover menú de categorías del admin
     */
    public function removeCategoriesMenu(): void
    {
        // Remover submenú de categorías bajo Posts
        \remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=category');
    }

    /**
     * Remover meta box de categorías
     */
    public function removeCategoriesMetaBox(): void
    {
        \remove_meta_box('categorydiv', 'post', 'side');
    }

    /**
     * Remover columnas de categorías en admin
     */
    public function removeCategoriesColumns(string|array $columns): array|string
    {
        if (\is_array($columns)) {
            unset($columns['categories']);
        }

        return $columns;
    }

    /**
     * Desregistrar widgets de categorías
     */
    public function unregisterCategoryWidgets(): void
    {
        \unregister_widget('WP_Widget_Categories');
    }
}
