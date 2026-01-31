<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cache Activation
    |--------------------------------------------------------------------------
    |
    | Controla si el sistema de cache está activo. Por defecto, activo solo
    | en producción (cuando WP_DEBUG es false)
    |
    */
    'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Cache Path
    |--------------------------------------------------------------------------
    |
    | Directorio donde se almacenarán los archivos de cache
    |
    */
    'path' => \Silmaril\Core\Helpers\Filesystem::folder('bootstrap/cache'),

    /*
    |--------------------------------------------------------------------------
    | Cache Lifetime
    |--------------------------------------------------------------------------
    |
    | Cuánto tiempo dura la cache antes de regenerarse automáticamente
    | null = nunca expira (requiere regeneración manual)
    |
    */
    'lifetime' => null, // En segundos, null para infinito

    /*
    |--------------------------------------------------------------------------
    | Cache Components
    |--------------------------------------------------------------------------
    |
    | Qué componentes se deben cachear
    |
    */
    'components' => [
        'config' => true,           // Configuraciones
        'providers' => true,        // Service providers
        'services' => true,         // Services
        'post_types' => true,       // Custom post types
        'taxonomies' => true,       // Custom taxonomies
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto-Regenerate
    |--------------------------------------------------------------------------
    |
    | Regenerar automáticamente la cache si detecta cambios en archivos
    |
    */
    'auto_regenerate' => false,  // true = checkea timestamps, false = manual

    /*
    |--------------------------------------------------------------------------
    | Cache Key
    |--------------------------------------------------------------------------
    |
    | Clave única para invalidar cache (cambiar para forzar regeneración)
    |
    */
    'key' => 'silmaril-v1.0.0',

    /*
    |--------------------------------------------------------------------------
    | Namespace Cache Generators
    |--------------------------------------------------------------------------
    |
    | Clave única para invalidar cache (cambiar para forzar regeneración).
    | Cambiar {component} por el nombre del compoente de la lista.
    | Ej. component = post_types Genera Silmaril\\Core\\Cache\\PostTypesCacheGenerator
    | Ej. component = taxonomies Genera Silmaril\\Core\\Cache\\TaxonomiesCacheGenerator
    |
    */
    'namespace_cache_genetors' => [
        'Silmaril\\Core\\Cache\\{component}CacheGenerator',
    ],
];
