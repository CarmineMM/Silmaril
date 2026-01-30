<?php

return [
    'frontend' => [
        'styles' => [
            'front-css' => [
                'src' => get_theme_file_uri('App/assets/main.css'),
                'deps' => [],
                'ver' => '1.0.0',
                'media' => 'all',
            ],
        ],
        'scripts' => [
            'front-js' => [
                'src' => get_theme_file_uri('App/assets/main.js'),
                'deps' => ['jquery'],
                'ver' => '1.0.0',
                'args' => [ // { 'strategy': string, 'in_footer': bool, 'fetchpriority': string }
                    // 'in_footer' => true,
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

    // 'localize' => [
    //     'front-js' => [
    //         'front_object' => [
    //             'Value 1' => 'Value 1',
    //             'Value 2' => 'Value 2',
    //         ],
    //     ],
    // ],
];
