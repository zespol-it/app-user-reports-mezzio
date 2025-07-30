<?php

declare(strict_types=1);

use Doctrine\DBAL\Driver\PDO\SQLite\Driver;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;

return [
    'doctrine' => [
        'connection'    => [
            'orm_default' => [
                'driverClass' => Driver::class,
                'params'      => [
                    'path' => __DIR__ . '/../../data/database.sqlite',
                ],
            ],
        ],
        'driver'        => [
            'orm_default' => [
                'class' => AttributeDriver::class,
                'paths' => [__DIR__ . '/../../src/Entity'],
            ],
        ],
        'entitymanager' => [
            'orm_default' => [
                'connection'    => 'orm_default',
                'configuration' => 'orm_default',
            ],
        ],
        'configuration' => [
            'orm_default' => [
                'metadata_cache'              => 'array',
                'query_cache'                 => 'array',
                'result_cache'                => 'array',
                'hydration_cache'             => 'array',
                'driver'                      => 'orm_default',
                'auto_generate_proxy_classes' => true,
                'proxy_dir'                   => __DIR__ . '/../../data/doctrine/proxy',
                'proxy_namespace'             => 'DoctrineProxy',
                'entity_namespaces'           => [
                    'App' => 'App\Entity',
                ],
            ],
        ],
    ],
];
