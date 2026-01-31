<?php

return [
    /*
    |--------------------------------------------------------------------------
    | REST API Base
    |--------------------------------------------------------------------------
    |
    | Configuración base para endpoints personalizados
    |
    */
    'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Site Configuration Endpoint
    |--------------------------------------------------------------------------
    |
    | Qué datos incluir en el endpoint de configuración del sitio
    |
    */
    'site_config' => [
        // Información básica
        'basic' => [
            'site_title' => true,
            'site_description' => true,
            'site_url' => true,
            'home_url' => true,
            'admin_email' => false,  // false por seguridad
            'charset' => true,
            'language' => true,
            'timezone' => true,
            'date_format' => true,
            'time_format' => true,
            'start_of_week' => true,
            'posts_per_page' => true,
        ],

        // Logo y branding
        'branding' => [
            'logo' => true,              // Custom logo
            'site_icon' => true,         // Favicon/site icon
            'custom_logo' => true,       // ID del logo
            'header_text' => true,       // Mostrar texto en header
            'background_image' => false, // Imagen de fondo
        ],

        // SEO y metadata
        'seo' => [
            'meta_description' => true,
            'meta_keywords' => false,
            'og_image' => true,          // Open Graph image
            'twitter_card' => true,
            'canonical_url' => true,
        ],

        // Contacto
        'contact' => [
            'phone' => true,
            'email' => true,
            'address' => true,
            'social_media' => true,      // Redes sociales
        ],

        // Analytics y tracking
        'analytics' => [
            'google_analytics' => true,
            'google_tag_manager' => true,
            'facebook_pixel' => true,
            'hotjar' => false,
        ],

        // Redes sociales
        'social' => [
            'facebook' => true,
            'twitter' => true,
            'instagram' => true,
            'linkedin' => true,
            'youtube' => true,
            'pinterest' => false,
            'github' => false,
        ],

        // Menús registrados
        'menus' => true,

        // Widgets areas
        'sidebars' => false,

        // Theme mods (Customizer)
        'theme_mods' => true,

        // Permalinks
        'permalinks' => [
            'structure' => true,
            'category_base' => true,
            'tag_base' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Configuración de cache para endpoints REST
    |
    */
    'cache' => [
        'enabled' => !WP_DEBUG,
        'ttl' => 3600,  // 1 hora en segundos
    ],

    /*
    |--------------------------------------------------------------------------
    | CORS Settings
    |--------------------------------------------------------------------------
    |
    | Configuración para Cross-Origin Resource Sharing
    |
    */
    'cors' => [
        'enabled' => true,
        'allowed_origins' => [
            '*',  // Cambiar a tu dominio de frontend en producción
            // 'https://your-frontend.com',
        ],
        'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
        'allowed_headers' => ['Content-Type', 'Authorization', 'X-WP-Nonce'],
        'expose_headers' => ['X-WP-Total', 'X-WP-TotalPages'],
        'max_age' => 86400,  // 24 horas
        'allow_credentials' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    'rate_limit' => [
        'enabled' => !WP_DEBUG,
        'limit' => 100,      // Requests por hora
        'interval' => 3600,  // Segundos
    ],
];
