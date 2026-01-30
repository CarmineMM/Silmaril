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
            'disable_for_posts' => true,             // Deshabilitar para posts
            'remove_admin_menu' => true,             // Remover del menú admin
            'remove_admin_meta_box' => true,         // Remover meta box
            'remove_admin_columns' => true,          // Remover columnas
            'remove_category_feed' => true,          // Remover feed de categorías
            'remove_category_widgets' => true,       // Remover widgets relacionados
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
            'disable_for_posts' => true,             // Deshabilitar para posts
            'remove_admin_menu' => true,             // Remover del menú admin
            'remove_admin_meta_box' => true,         // Remover meta box
            'remove_admin_columns' => true,          // Remover columnas
            'remove_tag_feed' => true,               // Remover feed de tags
            'remove_tag_widgets' => true,            // Remover widgets relacionados
        ],

        /*
        |--------------------------------------------------------------------------
        | Additional Cleanups
        |--------------------------------------------------------------------------
        */
        'additional' => [
            'remove_author_archives' => false,       // Remover archives de autores
            'remove_date_archives' => false,         // Remover archives por fecha
            'remove_trackbacks' => true,             // Remover trackbacks
            'remove_pingbacks' => true,              // Remover pingbacks
        ],
    ],
];
