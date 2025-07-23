<?php

declare(strict_types=1);

namespace App\Factory;

use App\Handler\UserHandler;
use App\Handler\EducationHandler;
use App\Handler\ExportHandler;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;

class HandlerFactory
{
    public static function createUserHandler(ContainerInterface $container): UserHandler
    {
        $entityManager = $container->get(EntityManager::class);
        return new UserHandler($entityManager);
    }

    public static function createEducationHandler(ContainerInterface $container): EducationHandler
    {
        $entityManager = $container->get(EntityManager::class);
        return new EducationHandler($entityManager);
    }

    public static function createExportHandler(ContainerInterface $container): ExportHandler
    {
        $entityManager = $container->get(EntityManager::class);
        return new ExportHandler($entityManager);
    }
} 