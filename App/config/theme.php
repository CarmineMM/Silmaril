<?php

/**
 * Configuraciones del tema
 */
return [
    /*
    |--------------------------------------------------------------------------
    | Road Tracer
    |--------------------------------------------------------------------------
    |
    | Controla el sistema de road tracer del tema
    |
    */
    'road_tracer' => WP_DEBUG,

    /*
    |--------------------------------------------------------------------------
    | Debug
    |--------------------------------------------------------------------------
    |
    | Controla el sistema de debug del tema
    |
    */
    'debug' => WP_DEBUG,

    /*
    |--------------------------------------------------------------------------
    | Language Path
    |--------------------------------------------------------------------------
    |
    | Ruta del directorio de idiomas
    |
    */
    'language_path' => 'App/lang',

    /*
    |--------------------------------------------------------------------------
    | Caracteristicas activas de Wordpress (Se pueden deshabilitar)
    |--------------------------------------------------------------------------
    |
    | Controla todo el sistema activo de Wordpress, permititendo deshabilitarlas.
    | Todas las caracteristicas estan activas por defecto.
    |
    */
    'features' => [
        /*
        |--------------------------------------------------------------------------
        | Comments System
        |--------------------------------------------------------------------------
        |
        | Controla todo el sistema de comentarios de WordPress
        |
        */
        'comments' => [
            'disable_globally' => false,              // Deshabilitar completamente (Todas las features entran en 'true')
            'disable_front_theme' => false,           // Deshabilitar completamente los comentarios (frontend + backend)
            'remove_admin_menu' => false,             // Remover del menú admin
            'remove_admin_columns' => false,          // Remover columnas al guardar posts
            'remove_admin_support' => false,          // Remover support en post types
            'remove_discussion_settings' => false,    // Remover settings de discusión
            'remove_recent_comments_widget' => false, // Remover widget
            'remove_comment_feed' => false,           // Remover feed de comentarios
        ],

        /*
        |--------------------------------------------------------------------------
        | Post Categories
        |--------------------------------------------------------------------------
        |
        | Controla las categorías de los posts
        |
        */
        'categories' => [
            'disable_globally' => false,             // Deshabilitar completamente (Todas las features entran en 'true')
            'disable_for_posts' => false,            // Deshabilitar para posts
            'remove_admin_menu' => false,            // Remover del menú admin
            'remove_admin_meta_box' => false,        // Remover meta box
            'remove_admin_columns' => false,         // Remover columnas
            'remove_category_feed' => false,         // Remover feed de categorías
            'remove_category_widgets' => false,      // Remover widgets relacionados
        ],

        /*
        |--------------------------------------------------------------------------
        | Post Tags
        |--------------------------------------------------------------------------
        |
        | Controla las etiquetas (tags) de los posts
        |
        */
        'tags' => [
            'disable_globally' => false,              // Deshabilitar completamente (Todas las features entran en 'true')
            'disable_for_posts' => false,             // Deshabilitar para posts
            'remove_admin_menu' => false,             // Remover del menú admin
            'remove_admin_meta_box' => false,         // Remover meta box
            'remove_admin_columns' => false,          // Remover columnas
            'remove_tag_feed' => false,               // Remover feed de tags
            'remove_tag_widgets' => false,            // Remover widgets relacionados
        ],

        /*
        |--------------------------------------------------------------------------
        | Additional Cleanups
        |--------------------------------------------------------------------------
        */
        'additional' => [
            'remove_pingbacks' => false,             // Remover pingbacks
            'remove_author_archives' => false,       // Remover archives de autores
            'remove_date_archives' => false,         // Remover archives por fecha
            'remove_trackbacks' => false,            // Remover trackbacks
        ],

        /*
        |--------------------------------------------------------------------------
        | Additional Cleanups
        |--------------------------------------------------------------------------
        */
        'editor' => [
            'disabled_gutenberg' => false,          // Deshabilitar Gutenberg globalmente
            'disabled_gutenberg_for' => [],         // Deshabilitar Gutenberg para post types (Si esta vacio, se desabilita para todos).
            'disabled_gutenberg_widgets' => false,  // Deshabilitar Gutenberg en widgets
            'use_classic_editor_plugin' => false   // true = requiere plugin instalado: https://wordpress.org/plugins/classic-editor/
        ],
    ],
];
