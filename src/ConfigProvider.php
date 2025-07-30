<?php

declare(strict_types=1);

namespace App;

use App\Factory\EntityManagerFactory;
use App\Factory\HandlerFactory;
use App\Handler\EducationHandler;
use App\Handler\ExportHandler;
use App\Handler\HomePageHandler;
use App\Handler\HomePageHandlerFactory;
use App\Handler\PingHandler;
use App\Handler\UserHandler;
use App\Middleware\JsonBodyParserMiddleware;
use Doctrine\DBAL\Driver\PDO\SQLite\Driver;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Laminas\ServiceManager\Factory\InvokableFactory;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'doctrine'     => $this->getDoctrineConfig(),
            'templates'    => $this->getTemplates(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'invokables' => [
                PingHandler::class => PingHandler::class,
            ],
            'factories'  => [
                EntityManager::class            => EntityManagerFactory::class,
                UserHandler::class              => [HandlerFactory::class, 'createUserHandler'],
                EducationHandler::class         => [HandlerFactory::class, 'createEducationHandler'],
                ExportHandler::class            => [HandlerFactory::class, 'createExportHandler'],
                HomePageHandler::class          => HomePageHandlerFactory::class,
                JsonBodyParserMiddleware::class => InvokableFactory::class,
            ],
        ];
    }

    public function getDoctrineConfig(): array
    {
        return [
            'connection'    => [
                'orm_default' => [
                    'driverClass' => Driver::class,
                    'params'      => [
                        'path' => __DIR__ . '/../data/database.sqlite',
                    ],
                ],
            ],
            'driver'        => [
                'orm_default' => [
                    'class' => AttributeDriver::class,
                    'paths' => [__DIR__ . '/Entity'],
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
                    'proxy_dir'                   => __DIR__ . '/../data/doctrine/proxy',
                    'proxy_namespace'             => 'DoctrineProxy',
                    'entity_namespaces'           => [
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
