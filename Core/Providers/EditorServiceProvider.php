<?php

namespace Silmaril\Core\Providers;

use Silmaril\Core\Foundation\ServiceProvider;

class EditorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->theme->config('theme.editor.disabled_gutenberg', false)) {
            // 4. Remover assets de Gutenberg
            \add_action('wp_enqueue_scripts', [$this, 'removeEnqueuedGutenbergAssets'], 50);

            \add_action('admin_enqueue_scripts', [$this, 'removeEnqueuedGutenbergAssetsAdmin'],  50);
        }
    }

    public function boot(): void
    {
        if ($this->theme->config('theme.editor.disabled_gutenberg', false)) {
            \add_filter('use_block_editor_for_post_type', [$this, 'disabledGutenbergForPostTypes'], 10, 2);
        }

        if ($this->theme->config('theme.editor.disabled_gutenberg_widgets', false)) {
            \add_filter('gutenberg_use_widgets_block_editor', '__return_false');
            \add_filter('use_widgets_block_editor', '__return_false');
            \add_filter('gutenberg_use_widgets_customizer', '__return_false');
            \add_filter('use_widgets_customizer', '__return_false');
        }
    }

    /**
     * Disable Gutenberg for specific post types.
     *
     * @param bool $can_edit
     * @param string $post_type
     * @return bool
     */
    public function disabledGutenbergForPostTypes(bool $can_edit, string $post_type): bool
    {
        $post_types_to_disable = $this->theme->config('theme.editor.disabled_gutenberg_for', []);

        if (\count($post_types_to_disable) < 1 || \in_array($post_type, $post_types_to_disable, true)) {
            return false;
        }

        return $can_edit;
    }

    /**
     * Remove enqueued Gutenberg assets.
     */
    public function removeEnqueuedGutenbergAssets(): void
    {
        \wp_dequeue_style('wp-block-library');
        \wp_dequeue_style('wp-block-library-theme');
        \wp_dequeue_style('wc-blocks-style'); // Si se usa WooCommerce
    }

    /**
     * Remove admin assets
     */
    public function removeEnqueuedGutenbergAssetsAdmin(): void
    {
        \wp_dequeue_style('wp-block-library');
        \wp_dequeue_style('wp-block-library-theme');
    }
}
