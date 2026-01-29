<?php

return [
    [
        'filter' => 'body_class',
        'callback' => [\Silmaril\Core\Filters\PageContentFilter::class, 'addBodyClasses'],
        'priority' => 1,
        'args' => 1,
    ]
];
