<?php

use Silmaril\App\Hooks\RemoveActionsHook;
use Silmaril\Core\Hooks\HtmlContentHook;
use Silmaril\App\Hooks\RestApiInitHook;

return [
    [
        'hook' => 'wp_head',
        'callback' => [HtmlContentHook::class, 'addHeadContent'],
        'priority' => 1,
        'args' => 0,
    ],
    [
        'hook' => 'init',
        'callback' => [RemoveActionsHook::class, 'initActions'],
        'priority' => 10,
        'args' => 0,
    ],
    [
        'hook' => 'rest_api_init',
        'callback' => [RestApiInitHook::class, 'getFeatureMedia'],
        'priority' => 10,
        'args' => 0,
    ],
];
