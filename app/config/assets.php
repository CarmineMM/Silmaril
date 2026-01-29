<?php

return [
    'frontend' => [
        'styles' => [
            'main' => [
                'src' => get_theme_file_uri('app/assets/main.css'),
                'deps' => [],
                'ver' => '1.0.0',
                'media' => 'all',
            ],
        ],
        'scripts' => [
            'main' => [
                'src' => get_theme_file_uri('app/assets/main.js'),
                'deps' => [],
                'ver' => '1.0.0',
                'args' => [ // { 'strategy': string, 'in_footer': bool, 'fetchpriority': string }
                    'in_footer' => true,
                ],
            ],
        ],
    ],

    // Admin assets
    'admin' => [
        // 'styles' => [
        //     'admin' => [
        //         'path' => '/dist/css/admin.css',
        //         'deps' => [],
        //     ],
        // ],

        // 'scripts' => [
        //    'main' => [
        //         'src' => get_theme_file_uri('app/assets/main.js'),
        //         'deps' => [],
        //         'ver' => '1.0.0',
        //         'args' => [ // { 'strategy': string, 'in_footer': bool, 'fetchpriority': string }
        //             'in_footer' => true,
        //         ],
        //     ],
        // ],
    ],
];
