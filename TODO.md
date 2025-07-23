# TODO - User Reports Mezzio Application

## Zadania do wykonania

### ✅ Zakończone

- [x] Utworzenie nowego projektu Laminas Mezzio
- [x] Instalacja wymaganych pakietów (Doctrine ORM, PhpSpreadsheet, Dompdf)
- [x] Konfiguracja Doctrine z SQLite
- [x] Utworzenie encji User i Education z atrybutami Doctrine
- [x] Implementacja UserHandler z pełnym CRUD
- [x] Implementacja EducationHandler z pełnym CRUD
- [x] Dodanie funkcjonalności sortowania w API
- [x] Dodanie funkcjonalności filtrowania w API
- [x] Dodanie funkcjonalności paginacji w API
- [x] Implementacja eksportu do XLS
- [x] Implementacja eksportu do PDF z obsługą polskich znaków
- [x] Utworzenie middleware do parsowania JSON
- [x] Konfiguracja routingu dla wszystkich endpointów
- [x] Utworzenie szablonu dashboard z interfejsem CRUD
- [x] Utworzenie szablonu dokumentacji API
- [x] Implementacja funkcji seed dla przykładowych danych
- [x] Napisanie testów PHPUnit dla UserHandler
- [x] Utworzenie README.md z instrukcjami
- [x] Utworzenie TODO.md i DONE.md
- [x] Dodanie popupów z komunikatami sukcesu/błędu

### 🔄 W trakcie

- [x] Testowanie aplikacji end-to-end
- [x] Optymalizacja wydajności

### 📋 Do wykonania

#### Priorytet Wysoki
- [x] Dodanie walidacji danych wejściowych
- [ ] Implementacja autoryzacji i uwierzytelniania
- [ ] Dodanie logowania błędów
- [ ] Utworzenie migracji bazy danych
- [x] Dodanie testów integracyjnych

#### Priorytet Średni
- [ ] Dodanie cache'owania
- [ ] Implementacja rate limiting
- [x] Dodanie dokumentacji API w formacie OpenAPI/Swagger
- [ ] Utworzenie CLI commands
- [ ] Dodanie monitoringu i metryk

#### Priorytet Niski
- [ ] Dodanie wsparcia dla innych baz danych (MySQL, PostgreSQL)
- [ ] Implementacja websocketów dla real-time updates
- [ ] Dodanie wsparcia dla GraphQL
- [ ] Utworzenie aplikacji mobilnej
- [ ] Dodanie wsparcia dla wielu języków

### 🐛 Błędy do naprawienia

- [x] Sprawdzenie kompatybilności z PHP 8.4
- [ ] Optymalizacja zapytań SQL
- [x] Poprawienie obsługi błędów w frontend

### 📚 Dokumentacja

- [x] Dodanie przykładów użycia API
- [ ] Utworzenie diagramów architektury
- [x] Dodanie instrukcji deploymentu
- [x] Utworzenie przewodnika dla developerów

### 🧪 Testy

- [x] Dodanie testów dla EducationHandler
- [x] Utworzenie testów integracyjnych
- [ ] Dodanie testów wydajnościowych
- [x] Utworzenie testów end-to-end

### 🔧 Narzędzia deweloperskie

- [x] Konfiguracja PHPStan/Psalm
- [ ] Dodanie pre-commit hooks
- [x] Konfiguracja CI/CD
- [ ] Dodanie Docker support

## Uwagi

- Projekt jest zbudowany na Laminas Mezzio 3.x z PHP 8.1+
- Używa Doctrine ORM 3.x z atrybutami (nowoczesna składnia)
- Baza danych SQLite jest używana dla prostoty
- Frontend używa Bootstrap 5 i vanilla JavaScript
- Wszystkie endpointy API zwracają JSON
- Eksport XLS i PDF jest zaimplementowany
- Testy używają PHPUnit z mockami

## Następne kroki

1. Implementacja autoryzacji
2. Utworzenie migracji bazy danych
3. Dodanie testów integracyjnych 