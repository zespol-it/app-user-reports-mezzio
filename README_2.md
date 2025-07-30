# User Reports Mezzio Application

## 📋 Opis projektu

Aplikacja do zarządzania użytkownikami z funkcjonalnością raportowania, sortowania, filtrowania i eksportu danych. Zbudowana na nowoczesnym stacku technologicznym z wykorzystaniem Laminas Mezzio 3.x i PHP 8.2+.

## 🚀 Jak powstał projekt

### 1. Inicjalizacja projektu
```bash
# Utworzenie nowego projektu Mezzio
composer create-project mezzio/mezzio-skeleton app-user-reports-mezzio

# Przejście do katalogu projektu
cd app-user-reports-mezzio

# Włączenie trybu deweloperskiego
composer development-enable
```

### 2. Instalacja zależności
```bash
# Instalacja Doctrine ORM
composer require doctrine/orm

# Instalacja bibliotek do eksportu
composer require phpoffice/phpspreadsheet
composer require dompdf/dompdf

# Instalacja walidacji
composer require laminas/laminas-inputfilter

# Instalacja narzędzi deweloperskich
composer require --dev phpunit/phpunit
composer require --dev vimeo/psalm
composer require --dev laminas/laminas-coding-standard
```

### 3. Konfiguracja Doctrine
```bash
# Utworzenie pliku konfiguracyjnego Doctrine
# config/autoload/doctrine.global.php
```

### 4. Tworzenie encji
```bash
# Utworzenie encji User
# src/Entity/User.php

# Utworzenie encji Education  
# src/Entity/Education.php
```

### 5. Implementacja handlerów
```bash
# Utworzenie UserHandler
# src/Handler/UserHandler.php

# Utworzenie EducationHandler
# src/Handler/EducationHandler.php

# Utworzenie ExportHandler
# src/Handler/ExportHandler.php
```

### 6. Konfiguracja routingu
```bash
# Dodanie tras w config/routes.php
```

### 7. Tworzenie frontendu
```bash
# Utworzenie dashboard.html
# public/dashboard.html

# Utworzenie strony głównej
# templates/app/home-page.phtml
```

### 8. Implementacja testów
```bash
# Utworzenie testów dla handlerów
# test/Handler/UserHandlerTest.php
# test/Handler/EducationHandlerTest.php
# test/Handler/ExportHandlerTest.php
```

### 9. Inicjalizacja bazy danych
```bash
# Utworzenie skryptu inicjalizacji
# bin/init-database.php

# Uruchomienie inicjalizacji
php bin/init-database.php
```

## 🏗️ Architektura aplikacji

### Struktura katalogów
```
app-user-reports-mezzio/
├── src/
│   ├── Entity/           # Encje Doctrine
│   │   ├── User.php
│   │   └── Education.php
│   ├── Handler/          # Handlery API
│   │   ├── UserHandler.php
│   │   ├── EducationHandler.php
│   │   ├── ExportHandler.php
│   │   └── UserInputFilter.php
│   ├── Factory/          # Factory dla dependency injection
│   │   ├── EntityManagerFactory.php
│   │   └── HandlerFactory.php
│   └── Middleware/       # Middleware
│       └── JsonBodyParserMiddleware.php
├── config/               # Konfiguracja aplikacji
│   ├── autoload/
│   │   ├── doctrine.global.php
│   │   └── dependencies.global.php
│   └── routes.php
├── public/               # Pliki publiczne
│   ├── dashboard.html    # Dashboard aplikacji
│   └── swagger.html      # Dokumentacja API
├── templates/            # Szablony
│   └── app/
│       └── home-page.phtml
├── test/                 # Testy
│   └── Handler/
├── bin/                  # Skrypty
│   └── init-database.php
└── data/                 # Dane aplikacji
    └── database.sqlite   # Baza danych SQLite
```

### Wzorce projektowe
- **PSR-15 Middleware** - architektura middleware
- **Dependency Injection** - wstrzykiwanie zależności
- **Repository Pattern** - dostęp do danych przez Doctrine
- **Factory Pattern** - tworzenie obiektów
- **Input Filter Pattern** - walidacja danych

## 🔧 Jak uruchomić projekt

### Wymagania systemowe
- PHP 8.1+
- Composer
- SQLite (wbudowany w PHP)

### Instalacja
```bash
# 1. Klonowanie projektu
git clone <repository-url>
cd app-user-reports-mezzio

# 2. Instalacja zależności
composer install

# 3. Inicjalizacja bazy danych
php bin/init-database.php

# 4. Uruchomienie serwera deweloperskiego
composer serve
```

### Dostęp do aplikacji
- **Strona główna**: http://localhost:8080
- **Dashboard**: http://localhost:8080/dashboard.html
- **Dokumentacja API**: http://localhost:8080/swagger.html
- **API Użytkownicy**: http://localhost:8080/api/users
- **API Wykształcenie**: http://localhost:8080/api/education

## 📚 API Endpoints

### Użytkownicy
```
GET    /api/users                    # Lista z paginacją
GET    /api/users?id={id}           # Pojedynczy użytkownik
POST   /api/users                   # Utwórz użytkownika
PUT    /api/users                   # Zaktualizuj użytkownika
DELETE /api/users?id={id}           # Usuń użytkownika
```

### Wykształcenie
```
GET    /api/education               # Lista wykształceń
GET    /api/education?id={id}       # Pojedyncze wykształcenie
POST   /api/education               # Utwórz wykształcenie
PUT    /api/education               # Zaktualizuj wykształcenie
DELETE /api/education?id={id}       # Usuń wykształcenie
```

### Eksport
```
GET    /api/export?format=xls       # Eksport do Excel
GET    /api/export?format=pdf       # Eksport do PDF
```

## 🛠️ Jak tworzyć nowe moduły i funkcjonalności

### 1. Tworzenie nowej encji

```bash
# Utworzenie encji Product
# src/Entity/Product.php
```

```php
<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'product')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $price;

    // Gettery i settery...
}
```

### 2. Tworzenie handlera

```bash
# Utworzenie ProductHandler
# src/Handler/ProductHandler.php
```

```php
<?php
declare(strict_types=1);

namespace App\Handler;

use App\Entity\Product;
use Doctrine\ORM\EntityManager;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ProductHandler implements RequestHandlerInterface
{
    public function __construct(
        private EntityManager $entityManager
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = $request->getMethod();
        
        return match ($method) {
            'GET' => $this->handleGet($request),
            'POST' => $this->handlePost($request),
            'PUT' => $this->handlePut($request),
            'DELETE' => $this->handleDelete($request),
            default => new JsonResponse(['error' => 'Method not allowed'], 405),
        };
    }

    // Implementacja metod CRUD...
}
```

### 3. Tworzenie InputFilter dla walidacji

```bash
# Utworzenie ProductInputFilter
# src/Handler/ProductInputFilter.php
```

```php
<?php
declare(strict_types=1);

namespace App\Handler;

use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\Input;
use Laminas\Validator;
use Laminas\Filter;

class ProductInputFilter extends InputFilter
{
    public function __construct()
    {
        // name
        $name = new Input('name');
        $name->getFilterChain()->attach(new Filter\StringTrim());
        $name->getValidatorChain()
            ->attach(new Validator\NotEmpty())
            ->attach(new Validator\StringLength(['max' => 255]));
        $this->add($name);

        // price
        $price = new Input('price');
        $price->getValidatorChain()
            ->attach(new Validator\NotEmpty())
            ->attach(new Validator\GreaterThan(['min' => 0]));
        $this->add($price);
    }
}
```

### 4. Dodanie routingu

```php
// config/routes.php
$app->route('/api/products', ProductHandler::class, ['GET', 'POST', 'PUT', 'DELETE'], 'api.products');
$app->route('/api/products/{id:\d+}', ProductHandler::class, ['GET'], 'api.products.id');
```

### 5. Konfiguracja dependency injection

```php
// config/autoload/dependencies.global.php
'factories' => [
    App\Handler\ProductHandler::class => App\Factory\HandlerFactory::class,
],
```

### 6. Tworzenie testów

```bash
# Utworzenie ProductHandlerTest
# test/Handler/ProductHandlerTest.php
```

```php
<?php
declare(strict_types=1);

namespace AppTest\Handler;

use App\Handler\ProductHandler;
use App\Entity\Product;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\TestCase;

class ProductHandlerTest extends TestCase
{
    // Implementacja testów...
}
```

### 7. Aktualizacja bazy danych

```bash
# Dodanie tabeli do bazy danych
# Można użyć Doctrine Migrations lub ręcznie dodać do init-database.php
```

## 🧪 Testowanie

### Uruchomienie testów
```bash
# Wszystkie testy
composer test

# Testy z pokryciem
composer test-coverage

# Statyczna analiza kodu
composer static-analysis
```

### Struktura testów
- **UserHandlerTest** - testy CRUD dla użytkowników
- **EducationHandlerTest** - testy CRUD dla wykształcenia
- **ExportHandlerTest** - testy eksportu

## 🔍 Narzędzia deweloperskie

### Analiza kodu
```bash
# PHPStan/Psalm
composer static-analysis

# Coding standards
composer cs-check
composer cs-fix
```

### Cache
```bash
# Czyszczenie cache
composer clear-config-cache
```

### Serwer deweloperski
```bash
# Uruchomienie serwera
composer serve

# Sprawdzenie statusu trybu deweloperskiego
composer development-status
```

## 📦 Deployment

### Środowisko produkcyjne
```bash
# Wyłączenie trybu deweloperskiego
composer development-disable

# Instalacja zależności produkcyjnych
composer install --no-dev --optimize-autoloader

# Ustawienie uprawnień
chmod -R 755 data/
```

### Konfiguracja serwera
- Skonfiguruj serwer web (Apache/Nginx) na katalog `public/`
- Ustaw zmienne środowiskowe
- Skonfiguruj bazę danych

## 🐛 Troubleshooting

### Problem z bazą danych
```bash
# Usuń starą bazę danych
rm data/database.sqlite

# Zainicjalizuj ponownie
php bin/init-database.php
```

### Problem z cache
```bash
# Wyczyść cache konfiguracji
composer clear-config-cache
```

### Problem z uprawnieniami
```bash
# Ustaw uprawnienia dla katalogu data
chmod -R 755 data/
```

## 📈 Rozwój aplikacji

### Dodawanie nowych funkcjonalności
1. **Utwórz encję** w `src/Entity/`
2. **Dodaj handler** w `src/Handler/`
3. **Stwórz InputFilter** dla walidacji
4. **Dodaj routing** w `config/routes.php`
5. **Napisz testy** w `test/Handler/`
6. **Zaktualizuj dokumentację**

### Best practices
- Używaj strict types
- Implementuj walidację danych
- Pisz testy dla nowych funkcjonalności
- Dokumentuj API
- Używaj dependency injection
- Przestrzegaj PSR-12

## 📄 Licencja

BSD-3-Clause

## 👥 Autor

Grzegorz Skotniczny