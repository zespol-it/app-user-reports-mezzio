# Aplikacja Raportów Użytkowników - Laminas Mezzio

Aplikacja do zarządzania użytkownikami z funkcjonalnością raportowania, sortowania, filtrowania i eksportu danych.

## Funkcjonalności

- ✅ **CRUD dla użytkowników** - pełne operacje Create, Read, Update, Delete
- ✅ **CRUD dla wykształcenia** - zarządzanie słownikiem wykształcenia
- ✅ **Sortowanie** - po wszystkich kolumnach (rosnąco/malejąco)
- ✅ **Filtrowanie** - po wszystkich polach użytkownika
- ✅ **Paginacja** - stronicowanie wyników
- ✅ **Eksport XLS** - eksport danych do formatu Excel
- ✅ **Eksport PDF** - eksport danych do formatu PDF z polskimi znakami
- ✅ **Dashboard** - interfejs webowy do zarządzania
- ✅ **REST API** - pełne API dla wszystkich operacji
- ✅ **Testy** - testy jednostkowe dla wszystkich handlerów

## Wymagania systemowe

- PHP 8.1 lub nowszy
- Composer
- SQLite (wbudowany w PHP)

## Instalacja

1. **Klonowanie projektu**
   ```bash
   git clone <repository-url>
   cd app-user-reports-mezzio
   ```

2. **Instalacja zależności**
   ```bash
   composer install
   ```

3. **Inicjalizacja bazy danych**
   ```bash
   php bin/init-database.php
   ```

4. **Uruchomienie serwera deweloperskiego**
   ```bash
   composer serve
   ```

5. **Otwarcie aplikacji**
   - Dashboard: http://localhost:8080/dashboard.html
   - API: http://localhost:8080/api/users

## Struktura projektu

```
app-user-reports-mezzio/
├── src/
│   ├── Entity/           # Encje Doctrine
│   │   ├── User.php
│   │   └── Education.php
│   ├── Handler/          # Handlery API
│   │   ├── UserHandler.php
│   │   ├── EducationHandler.php
│   │   └── ExportHandler.php
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
│   └── dashboard.html    # Dashboard aplikacji
├── test/                 # Testy
│   └── Handler/
│       ├── UserHandlerTest.php
│       └── EducationHandlerTest.php
├── bin/                  # Skrypty
│   └── init-database.php
└── data/                 # Dane aplikacji
    └── database.sqlite   # Baza danych SQLite
```

## API Endpoints

### Użytkownicy

- `GET /api/users` - Lista użytkowników z paginacją
- `GET /api/users?id={id}` - Pobierz użytkownika po ID
- `POST /api/users` - Utwórz nowego użytkownika
- `PUT /api/users` - Zaktualizuj użytkownika
- `DELETE /api/users?id={id}` - Usuń użytkownika

### Wykształcenie

- `GET /api/education` - Lista wszystkich wykształceń
- `GET /api/education?id={id}` - Pobierz wykształcenie po ID
- `POST /api/education` - Utwórz nowe wykształcenie
- `PUT /api/education` - Zaktualizuj wykształcenie
- `DELETE /api/education?id={id}` - Usuń wykształcenie

### Eksport

- `GET /api/export?format=xls` - Eksport do Excel
- `GET /api/export?format=pdf` - Eksport do PDF

## Parametry zapytań

### Filtrowanie
- `name` - filtruj po imieniu i nazwisku
- `phone_number` - filtruj po numerze telefonu
- `address` - filtruj po adresie
- `age` - filtruj po wieku
- `education_id` - filtruj po wykształceniu

### Sortowanie
- `sort_by` - kolumna do sortowania (id, name, phoneNumber, address, age)
- `sort_order` - kierunek sortowania (ASC, DESC)

### Paginacja
- `page` - numer strony (domyślnie 1)
- `limit` - liczba rekordów na stronę (domyślnie 10, max 100)

## Przykłady użycia API

### Pobieranie użytkowników z filtrowaniem i sortowaniem
```bash
curl "http://localhost:8080/api/users?name=Jan&sort_by=age&sort_order=DESC&page=1&limit=5"
```

### Tworzenie nowego użytkownika
```bash
curl -X POST http://localhost:8080/api/users \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Jan Kowalski",
    "phone_number": "+48 123 456 789",
    "address": "ul. Testowa 1, Warszawa",
    "age": 30,
    "education_id": 3
  }'
```

### Eksport danych do Excel
```bash
curl "http://localhost:8080/api/export?format=xls&name=Jan" -o raport.xlsx
```

## Testy

Uruchomienie testów:
```bash
composer test
```

Testy pokrywają:
- ✅ Operacje CRUD dla użytkowników
- ✅ Operacje CRUD dla wykształcenia
- ✅ Obsługę błędów (404, 400, 405)
- ✅ Paginację i filtrowanie
- ✅ Walidację danych wejściowych

## Konfiguracja

### Baza danych
Aplikacja używa SQLite jako bazy danych. Plik bazy danych znajduje się w `data/database.sqlite`.

### Środowisko deweloperskie
```bash
composer development-enable
```

### Środowisko produkcyjne
```bash
composer development-disable
```

## Rozwój aplikacji

### Dodawanie nowych encji
1. Utwórz encję w `src/Entity/`
2. Dodaj handler w `src/Handler/`
3. Dodaj routing w `config/routes.php`
4. Napisz testy w `test/Handler/`

### Dodawanie nowych funkcjonalności
1. Utwórz handler lub middleware
2. Dodaj factory w `src/Factory/`
3. Skonfiguruj dependency injection
4. Dodaj routing
5. Napisz testy

## Troubleshooting

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

## Licencja

BSD-3-Clause

## Autor

(zespół-IT.pl) Grzegorz Skotniczny
