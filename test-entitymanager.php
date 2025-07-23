<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Factory\EntityManagerFactory;
use App\Entity\Education;
use App\Entity\User;

try {
    // Tworzenie EntityManager
    $container = new \Laminas\ServiceManager\ServiceManager();
    $container->setService('config', [
        'doctrine' => [
            'connection' => [
                'orm_default' => [
                    'driverClass' => \Doctrine\DBAL\Driver\PDO\SQLite\Driver::class,
                    'params' => [
                        'path' => __DIR__ . '/data/database.sqlite',
                    ],
                ],
            ],
            'driver' => [
                'orm_default' => [
                    'class' => \Doctrine\ORM\Mapping\Driver\AttributeDriver::class,
                    'paths' => [__DIR__ . '/src/Entity'],
                ],
            ],
        ],
    ]);

    $factory = new EntityManagerFactory();
    $entityManager = $factory($container);

    echo "EntityManager utworzony pomyślnie!\n";

    // Test pobierania danych
    $educations = $entityManager->getRepository(Education::class)->findAll();
    echo "Liczba wykształceń: " . count($educations) . "\n";

    $users = $entityManager->getRepository(User::class)->findAll();
    echo "Liczba użytkowników: " . count($users) . "\n";

    echo "Test zakończony pomyślnie!\n";

} catch (Exception $e) {
    echo "Błąd: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} 