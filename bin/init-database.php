<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Entity\User;
use App\Entity\Education;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;

// Konfiguracja Doctrine
$paths = [__DIR__ . '/../src/Entity'];
$isDevMode = true;

$config = new \Doctrine\ORM\Configuration();
$config->setMetadataDriverImpl(new AttributeDriver($paths));
$config->setProxyDir(__DIR__ . '/../data/doctrine/proxy');
$config->setProxyNamespace('DoctrineProxy');
$config->setAutoGenerateProxyClasses($isDevMode);

// Parametry połączenia z bazą danych
$connectionParams = [
    'driver' => 'pdo_sqlite',
    'path' => __DIR__ . '/../data/database.sqlite',
];

// Tworzenie EntityManager
$entityManager = new EntityManager(
    new \Doctrine\DBAL\Connection($connectionParams, new \Doctrine\DBAL\Driver\PDO\SQLite\Driver()),
    $config
);

// Tworzenie schematu bazy danych
$schemaTool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);
$metadata = $entityManager->getMetadataFactory()->getAllMetadata();

echo "Tworzenie schematu bazy danych...\n";
$schemaTool->createSchema($metadata);
echo "Schemat bazy danych został utworzony.\n";

// Dodawanie przykładowych danych
echo "Dodawanie przykładowych danych...\n";

// Dodawanie wykształceń
$educations = [
    'Podstawowe',
    'Średnie',
    'Wyższe - licencjat',
    'Wyższe - magister',
    'Doktorat'
];

$educationEntities = [];
foreach ($educations as $educationName) {
    $education = new Education();
    $education->setName($educationName);
    $entityManager->persist($education);
    $educationEntities[] = $education;
}

$entityManager->flush();
echo "Dodano " . count($educations) . " typów wykształcenia.\n";

// Dodawanie przykładowych użytkowników
$users = [
    [
        'name' => 'Jan Kowalski',
        'phone_number' => '+48 123 456 789',
        'address' => 'ul. Kwiatowa 1, 00-001 Warszawa',
        'age' => 30,
        'education' => $educationEntities[2] // Wyższe - licencjat
    ],
    [
        'name' => 'Anna Nowak',
        'phone_number' => '+48 987 654 321',
        'address' => 'ul. Słoneczna 15, 30-001 Kraków',
        'age' => 25,
        'education' => $educationEntities[3] // Wyższe - magister
    ],
    [
        'name' => 'Piotr Wiśniewski',
        'phone_number' => '+48 555 123 456',
        'address' => 'ul. Długa 42, 80-001 Gdańsk',
        'age' => 35,
        'education' => $educationEntities[1] // Średnie
    ],
    [
        'name' => 'Maria Wójcik',
        'phone_number' => '+48 777 888 999',
        'address' => 'ul. Krótka 7, 50-001 Wrocław',
        'age' => 28,
        'education' => $educationEntities[4] // Doktorat
    ],
    [
        'name' => 'Tomasz Lewandowski',
        'phone_number' => '+48 111 222 333',
        'address' => 'ul. Prosta 12, 40-001 Katowice',
        'age' => 22,
        'education' => $educationEntities[0] // Podstawowe
    ]
];

foreach ($users as $userData) {
    $user = new User();
    $user->setName($userData['name']);
    $user->setPhoneNumber($userData['phone_number']);
    $user->setAddress($userData['address']);
    $user->setAge($userData['age']);
    $user->setEducation($userData['education']);
    
    $entityManager->persist($user);
}

$entityManager->flush();
echo "Dodano " . count($users) . " przykładowych użytkowników.\n";

echo "Inicjalizacja bazy danych zakończona pomyślnie!\n";
echo "Baza danych znajduje się w: " . __DIR__ . '/../data/database.sqlite' . "\n"; 