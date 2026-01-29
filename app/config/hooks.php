<?php

use Silmaril\Core\Hooks\HtmlContentHook;

return [
    [
        'hook' => 'wp_head',
        'callback' => [HtmlContentHook::class, 'addHeadContent'],
        'priority' => 1,
        'args' => 0,
    ]
];
