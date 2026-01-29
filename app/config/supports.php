<?php

return [
    /**
     * Título dinámico del sitio.
     */
    'title-tag' => true,

    /**
     * Imágenes destacadas en los post types.
     */
    'post-thumbnails' => true,

    /**
     * Marcado HTML5 para formularios de búsqueda, comentarios, etc.
     */
    'html5' => [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ],

    /**
     * Logo personalizado
     */
    'custom-logo' => [
        'height' => 100,
        'width' => 400,
        'flex-width' => true,
        'flex-height' => true,
    ],

    /**
     * Fondo personalizado.
     */
    'custom-background' => [
        'default-color' => 'ffffff',
        'default-image' => '',
    ],

    /**
     * Contenido de los bloques de Gutenberg anchos.
     */
    'align-wide' => true,

    /**
     * Contenido incrustado receptivo.
     * Como youtube, vimeo, etc..
     */
    'responsive-embeds' => true,

    /**
     * Estilos de editor.
     */
    'editor-styles' => true,

    /**
     * Estilos de Gutenberg.
     */
    'wp-block-styles' => true,

    /**
     * Agrega enlaces predeterminados de fuentes RSS
     * de publicaciones y comentarios a la cabecera.
     */
    'automatic-feed-links' => true,

    /**
     * Agrega compatibilidad con temas para la actualización selectiva de widgets.
     *
     * @see https://make.wordpress.org/core/2016/03/22/implementing-selective-refresh-support-for-widgets/
     */
    'customize-selective-refresh-widgets' => true,
];
