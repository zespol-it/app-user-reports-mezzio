<?php

declare(strict_types=1);

namespace App\Factory;

use Doctrine\DBAL\Driver\PDO\SQLite\Driver;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Psr\Container\ContainerInterface;

class EntityManagerFactory
{
    public function __invoke(ContainerInterface $container): EntityManager
    {
        $config = $container->get('config');
        $doctrineConfig = $config['doctrine'];

        $connectionParams = $doctrineConfig['connection']['orm_default']['params'];
        $paths = $doctrineConfig['driver']['orm_default']['paths'];
        $isDevMode = true;

        $ormConfig = new \Doctrine\ORM\Configuration();
        $ormConfig->setMetadataDriverImpl(new AttributeDriver($paths));
        $ormConfig->setProxyDir(__DIR__ . '/../../data/doctrine/proxy');
        $ormConfig->setProxyNamespace('DoctrineProxy');
        $ormConfig->setAutoGenerateProxyClasses($isDevMode);

        return new EntityManager(
            new \Doctrine\DBAL\Connection($connectionParams, new \Doctrine\DBAL\Driver\PDO\SQLite\Driver()),
            $ormConfig
        );
    }
} 