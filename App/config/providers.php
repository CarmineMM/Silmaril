<?php

return [
    /**
     * Providers auto boot
     */
    'auto' => [
        \Silmaril\Core\Providers\SupportsServiceProvider::class,
        \Silmaril\Core\Providers\ThemeServiceProvider::class,
        \Silmaril\Core\Providers\AssetsServiceProvider::class,
        \Silmaril\Core\Providers\TaxonomyServiceProvider::class,
        \Silmaril\Core\Providers\PostTypeServiceProvider::class,
        \Silmaril\Core\Providers\HookServiceProvider::class,
        \Silmaril\Core\Providers\FilterServiceProvider::class,
    ],

    /**
     * Providers deferred boot
     */
    'deferred' => [],
];
