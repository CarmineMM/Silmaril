<?php

namespace Silmaril\App\Hooks;

class RemoveActionsHook
{
    /**
     * Acciones init
     */
    public static function initActions(): void
    {
        // 🔒 SEGURIDAD: Ocultar versión de WordPress
        remove_action('wp_head', 'wp_generator');

        // ⚠️ OBSOLETO: RSD (Really Simple Discovery) - clientes blogging antiguos
        remove_action('wp_head', 'rsd_link');

        // ⚠️ OBSOLETO: Windows Live Writer (descontinuado 2012)
        remove_action('wp_head', 'wlwmanifest_link');

        // ⚠️ OBSOLETO: Links de navegación antiguos (index, parent, start)
        remove_action('wp_head', 'index_rel_link');
        remove_action('wp_head', 'parent_post_rel_link');
        remove_action('wp_head', 'start_post_rel_link');

        // 🔄 DUPLICADO: Links prev/next (mejor implementar manualmente)
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');

        // 📉 USO REDUCIDO: Feeds RSS (solo necesario para blogs/news sites)
        remove_action('wp_head', 'feed_links_extra', 3);
        remove_action('wp_head', 'feed_links', 2);

        // 🐌 RENDIMIENTO: Emoji detection script (~3KB, bloquea renderizado)
        remove_action('wp_head', 'print_emoji_detection_script', 7);

        // 🎨 ESTILOS: CSS de emojis (~2KB, puede interferir con estilos propios)
        remove_action('wp_print_styles', 'print_emoji_styles');
    }
}
