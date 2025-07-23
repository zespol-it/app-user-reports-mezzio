# Laminas MVC vs Mezzio

## 🏗️ Laminas MVC

- **Typ:** Full-stack framework (monolityczny)
- **Architektura:** Oparta na wzorcu MVC (Model-View-Controller)
- **Routing:** Skonfigurowany przez tablice PHP lub pliki konfiguracyjne
- **Szablony:** Obsługuje np. PHP, Twig, itp.
- **Życie aplikacji:** Cięższy, bardziej zintegrowany
- **Typowe zastosowania:** Duże, klasyczne aplikacje z rozbudowanym interfejsem

## ⚡ Mezzio

- **Typ:** Microframework (PSR-15 middleware-based)
- **Architektura:** Oparta na middleware i PSR (bardziej nowoczesna)
- **Routing:** Oparty na trasach i funkcjach/middleware
- **Szablony:** Można używać dowolnego silnika szablonów, ale szablony są opcjonalne
- **Życie aplikacji:** Lekkie, bardziej modularne
- **Typowe zastosowania:** REST API, mikroserwisy, aplikacje oparte na middleware

## 📌 Podsumowanie różnic

| Cecha               | Laminas MVC             | Mezzio                         |
|---------------------|-------------------------|--------------------------------|
| Podejście           | MVC                     | Middleware (PSR-15)            |
| Styl konfiguracji   | Konfiguracja plikowa    | Programistyczna (faktory, DI)  |
| Lekkość             | Cięższy                 | Lekki                          |
| Modułowość          | Monolityczny            | Bardziej modularny             |
| Użycie PSR          | Częściowe               | Duży nacisk na PSR-7, PSR-15   |
| Typowe użycie       | Tradycyjne aplikacje    | API, mikroserwisy              |

## 🔍 Kiedy co wybrać?

- **Mezzio** – Nowoczesne podejście, idealne do API i mikroserwisów.
- **Laminas MVC** – Klasyczne aplikacje z kontrolerami, widokami i pełną strukturą MVC.


# app-user-reports-api

```bash
composer create-project laminas/laminas-mvc-skeleton app-user-reports-api

cd app-user-reports-api;
composer require doctrine/doctrine-orm-module laminas-api-tools/api-tools laminas/laminas-form phpoffice/phpspreadsheet dompdf/dompdf

php vendor/bin/doctrine-module orm:generate-entities module/Application/src/Entity --generate-annotations=true

mkdir app-user-reports-api/module/Application/src/Entity

php vendor/bin/doctrine-module orm:schema-tool:create
```

REST API dla encji user oraz education obsługuje już pełen CRUD (GET, POST, PUT, DELETE).
http://localhost/api/user[/:id]
http://localhost/api/education[/:id]

```bash
vendor\bin\phpunit
vendor\bin\phpunit --debug
```

# app-user-reports-mezzio

```bash
composer create-project mezzio/mezzio-skeleton app-user-reports-mezzio

cd app-user-reports-mezzio;
composer require doctrine/doctrine-orm-module laminas/laminas-form phpoffice/phpspreadsheet dompdf/dompdf
composer require laminas/laminas-inputfilter
composer require mezzio/mezzio-platesrenderer
```

```bash
composer serve
composer clear-config-cache
composer test
```
