<?php

return [
    /**
     * Providers auto boot
     */
    'auto' => [
        \Silmaril\Core\Providers\SupportsServiceProvider::class,
        \Silmaril\Core\Providers\ThemeServiceProvider::class,
    ],

    /**
     * Providers deferred boot
     */
    'deferred' => [],
];
