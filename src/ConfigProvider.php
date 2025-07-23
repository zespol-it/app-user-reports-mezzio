<?php

declare(strict_types=1);

namespace App;

use App\Factory\EntityManagerFactory;
use App\Factory\HandlerFactory;
use App\Handler\EducationHandler;
use App\Handler\ExportHandler;
use App\Handler\UserHandler;
use App\Middleware\JsonBodyParserMiddleware;
use Doctrine\ORM\EntityManager;


class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'doctrine' => $this->getDoctrineConfig(),
            'templates' => $this->getTemplates(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'invokables' => [
                \App\Handler\PingHandler::class => \App\Handler\PingHandler::class,
            ],
            'factories' => [
                EntityManager::class => EntityManagerFactory::class,
                UserHandler::class => [HandlerFactory::class, 'createUserHandler'],
                EducationHandler::class => [HandlerFactory::class, 'createEducationHandler'],
                ExportHandler::class => [HandlerFactory::class, 'createExportHandler'],
                \App\Handler\HomePageHandler::class => \App\Handler\HomePageHandlerFactory::class,
                JsonBodyParserMiddleware::class => \Laminas\ServiceManager\Factory\InvokableFactory::class,
            ],
        ];
    }

    public function getDoctrineConfig(): array
    {
        return [
            'connection' => [
                'orm_default' => [
                    'driverClass' => \Doctrine\DBAL\Driver\PDO\SQLite\Driver::class,
                    'params' => [
                        'path' => __DIR__ . '/../data/database.sqlite',
                    ],
                ],
            ],
            'driver' => [
                'orm_default' => [
                    'class' => \Doctrine\ORM\Mapping\Driver\AttributeDriver::class,
                    'paths' => [__DIR__ . '/Entity'],
                ],
            ],
            'entitymanager' => [
                'orm_default' => [
                    'connection' => 'orm_default',
                    'configuration' => 'orm_default',
                ],
            ],
            'configuration' => [
                'orm_default' => [
                    'metadata_cache' => 'array',
                    'query_cache' => 'array',
                    'result_cache' => 'array',
                    'hydration_cache' => 'array',
                    'driver' => 'orm_default',
                    'auto_generate_proxy_classes' => true,
                    'proxy_dir' => __DIR__ . '/../data/doctrine/proxy',
                    'proxy_namespace' => 'DoctrineProxy',
                    'entity_namespaces' => [
                        'App' => 'App\Entity',
                    ],
                ],
            ],
        ];
    }

    public function getTemplates(): array
    {
        return [
            'paths' => [
                'app'    => [__DIR__ . '/../templates/app'],
                'error'  => [__DIR__ . '/../templates/error'],
                'layout' => [__DIR__ . '/../templates/layout'],
            ],
        ];
    }


} 