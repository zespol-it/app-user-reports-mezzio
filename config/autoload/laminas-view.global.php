<?php

declare(strict_types=1);

return [
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../../templates/app',
            __DIR__ . '/../../templates/error',
            __DIR__ . '/../../templates/layout',
        ],
    ],
    'templates'    => [
        'paths' => [
            'app'    => [__DIR__ . '/../../templates/app'],
            'error'  => [__DIR__ . '/../../templates/error'],
            'layout' => [__DIR__ . '/../../templates/layout'],
        ],
    ],
    'laminas-view' => [
        'resolver' => [
            'template_path_stack' => [
                __DIR__ . '/../../templates/app',
                __DIR__ . '/../../templates/error',
                __DIR__ . '/../../templates/layout',
            ],
        ],
    ],
];
