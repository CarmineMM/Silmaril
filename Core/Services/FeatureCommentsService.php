<?php

namespace Silmaril\Core\Services;

use Silmaril\Core\Foundation\Service;

class FeatureCommentsService extends Service
{
    public function init(): void
    {
        $disabledAllFeatures = $this->theme->config('theme.features.comments.disable_globally', false);

        // Features individuales
        if ($this->theme->config('theme.features.comments.disable_front_theme', false) || $disabledAllFeatures) {
            \add_filter('comments_open', '__return_false', 200, 2);
            \add_filter('pings_open', '__return_false', 201, 2);
            \add_filter('comments_array', '__return_empty_array', 202, 2);

            // Remover CSS de comentarios del frontend
            \add_action('wp_print_styles', [$this, 'deregisterCommentStyles'], 200);

            // Remover scripts de comentarios
            \add_action('wp_print_scripts', [$this, 'dequeueCommentScripts'], 201);
        }

        if ($this->theme->config('theme.features.comments.remove_admin_menu', false) || $disabledAllFeatures) {
            \add_action('admin_menu', [$this, 'removeAdminMenu'], 202);
            \add_action('admin_init', [$this, 'removeAdminCommentsSupport'], 203);
        }

        if ($this->theme->config('theme.features.comments.remove_admin_columns', false) || $disabledAllFeatures) {
            \add_filter('manage_posts_columns', [$this, 'removeCommentsColumns'], 204);
            \add_filter('manage_pages_columns', [$this, 'removeCommentsColumns'], 205);
        }

        if ($this->theme->config('theme.features.comments.remove_admin_support', false) || $disabledAllFeatures) {
            \add_action('admin_init', [$this, 'removeCommentsSupport'], 203);
        }

        if ($this->theme->config('theme.features.comments.remove_discussion_settings', false) || $disabledAllFeatures) {
            \add_action('admin_menu', [$this, 'removeDiscussionSettings'], 204);
        }

        if ($this->theme->config('theme.features.comments.remove_recent_comments_widget', false) || $disabledAllFeatures) {
            \add_action('widgets_init', [$this, 'unregisterCommentsWidgets'], 205);
        }

        if ($this->theme->config('theme.features.comments.remove_comment_feed', false) || $disabledAllFeatures) {
            \add_filter('feed_links_show_comments_feed', '__return_false', 206);
            \remove_action('wp_head', 'feed_links_extra', 3);
        }
    }

    /**
     * Remove admin menu
     * 
     * @return void
     */
    public function removeAdminMenu(): void
    {
        \remove_menu_page('edit-comments.php');
    }

    /**
     * Remove support for comments in admin
     * 
     * @return void
     */
    public function removeAdminCommentsSupport(): void
    {
        // Remover de post types por defecto (Legacy support)
        \remove_post_type_support('post', 'comments');
        \remove_post_type_support('page', 'comments');
        \remove_post_type_support('attachment', 'comments');

        // Remover de todos los post types registrados
        foreach (\get_post_types() as $postType) {
            \remove_post_type_support($postType, 'comments');
            \remove_post_type_support($postType, 'trackbacks');
        }
    }

    /**
     * Remove admin coumns
     * 
     * @return array
     */
    public function removeCommentsColumns(array $columns): array
    {
        unset($columns['comments']);
        return $columns;
    }

    /**
     * Remover soporte de comentarios de todos los post types
     */
    public function removeCommentsSupport(): void
    {
        // Remover de post types por defecto
        \remove_post_type_support('post', 'comments');
        \remove_post_type_support('page', 'comments');
        \remove_post_type_support('attachment', 'comments');

        // Remover de custom post types
        foreach (\get_post_types() as $postType) {
            \remove_post_type_support($postType, 'comments');
            \remove_post_type_support($postType, 'trackbacks');
        }
    }

    /**
     * Remove discussion setting
     */
    public function removeDiscussionSettings(): void
    {
        \remove_submenu_page('options-general.php', 'options-discussion.php');
        \remove_submenu_page('edit-comments.php', 'edit-comments.php');
    }

    /**
     * Desregistrar widgets de comentarios
     */
    public function unregisterCommentsWidgets(): void
    {
        \unregister_widget('WP_Widget_Recent_Comments');
        \unregister_widget('WP_Widget_Comments');
    }

    /**
     * Desregistrar estilos de comentarios
     */
    public function deregisterCommentStyles(): void
    {
        \wp_deregister_style('wp-block-library');
        \wp_dequeue_style('comment-reply');
    }

    /**
     * Desencolar scripts de comentarios
     */
    public function dequeueCommentScripts(): void
    {
        \wp_dequeue_script('comment-reply');
    }
}
