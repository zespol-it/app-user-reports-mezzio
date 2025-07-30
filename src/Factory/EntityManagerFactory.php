<?php

declare(strict_types=1);

namespace App\Factory;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDO\SQLite\Driver;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Psr\Container\ContainerInterface;

class EntityManagerFactory
{
    public function __invoke(ContainerInterface $container): EntityManager
    {
        $config         = $container->get('config');
        $doctrineConfig = $config['doctrine'];

        $connectionParams = $doctrineConfig['connection']['orm_default']['params'];
        $paths            = $doctrineConfig['driver']['orm_default']['paths'];
        $isDevMode        = true;

        $ormConfig = new Configuration();
        $ormConfig->setMetadataDriverImpl(new AttributeDriver($paths));
        $ormConfig->setProxyDir(__DIR__ . '/../../data/doctrine/proxy');
        $ormConfig->setProxyNamespace('DoctrineProxy');
        $ormConfig->setAutoGenerateProxyClasses($isDevMode);

        return new EntityManager(
            new Connection($connectionParams, new Driver()),
            $ormConfig
        );
    }
}
