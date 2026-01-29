<?php

return [
    'frontend' => [
        'styles' => [
            'main' => [
                'path' => get_theme_file_uri('app/assets/main.css'),
                'deps' => [],
                'media' => 'all',
            ],
        ],
        // 'scripts' => [
        //     'main' => [
        //         'path' => '/dist/js/app.js',
        //         'deps' => ['jquery'],
        //         'footer' => true,
        //     ],
        // ],
    ],

    // Admin assets
    // 'admin' => [
    //     'styles' => [
    //         'admin' => [
    //             'path' => '/dist/css/admin.css',
    //             'deps' => [],
    //         ],
    //     ],

    //     'scripts' => [
    //         'admin' => [
    //             'path' => '/dist/js/admin.js',
    //             'deps' => ['jquery'],
    //             'footer' => true,
    //         ],
    //     ],
    // ],
];
