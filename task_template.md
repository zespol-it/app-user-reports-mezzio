# Task Template - Szablon realizacji zadań

## 📋 Struktura zadania

### 1. Analiza wymagań
- [ ] Przeczytaj dokładnie opis zadania
- [ ] Zidentyfikuj główne funkcjonalności do zaimplementowania
- [ ] Określ technologie i narzędzia do użycia
- [ ] Sprawdź czy masz wszystkie wymagane zależności
- [ ] Zidentyfikuj potencjalne wyzwania techniczne

### 2. Planowanie
- [ ] Podziel zadanie na mniejsze kroki
- [ ] Określ kolejność realizacji
- [ ] Oszacuj czas na każdy krok
- [ ] Zidentyfikuj punkty kontrolne
- [ ] Przygotuj plan testowania

### 3. Implementacja
- [ ] Utwórz nową gałąź (jeśli używasz Git)
- [ ] Zaimplementuj funkcjonalność krok po kroku
- [ ] Testuj po każdym kroku
- [ ] Dokumentuj zmiany
- [ ] Commituj zmiany regularnie

### 4. Testowanie
- [ ] Uruchom testy jednostkowe
- [ ] Przetestuj funkcjonalność ręcznie
- [ ] Sprawdź kompatybilność
- [ ] Przetestuj edge cases
- [ ] Sprawdź wydajność

### 5. Dokumentacja
- [ ] Zaktualizuj README.md jeśli potrzeba
- [ ] Dodaj komentarze do kodu
- [ ] Zaktualizuj TODO.md
- [ ] Stwórz dokumentację API jeśli nowe endpointy
- [ ] Zaktualizuj testy

### 6. Code Review
- [ ] Sprawdź kod pod kątem best practices
- [ ] Uruchom analizę statyczną (Psalm/PHPStan)
- [ ] Sprawdź standardy kodowania (PHPCS)
- [ ] Popraw błędy i ostrzeżenia
- [ ] Zoptymalizuj kod jeśli potrzeba

### 7. Finalizacja
- [ ] Uruchom wszystkie testy
- [ ] Sprawdź czy wszystko działa
- [ ] Przygotuj pull request (jeśli Git)
- [ ] Zaktualizuj status w TODO.md
- [ ] Przekaż do review

---

## 🎯 Przykłady zadań

### Zadanie: Dodanie nowej encji "Product"

#### Analiza wymagań
- [ ] Nowa encja Product z polami: id, name, price, description
- [ ] CRUD operacje dla Product
- [ ] Walidacja danych wejściowych
- [ ] Testy jednostkowe
- [ ] Dokumentacja API

#### Planowanie
- [ ] Utworzenie encji Product
- [ ] Utworzenie ProductHandler
- [ ] Utworzenie ProductInputFilter
- [ ] Dodanie routingu
- [ ] Implementacja testów
- [ ] Aktualizacja bazy danych

#### Implementacja
```bash
# 1. Utworzenie encji
# src/Entity/Product.php

# 2. Utworzenie handlera
# src/Handler/ProductHandler.php

# 3. Utworzenie InputFilter
# src/Handler/ProductInputFilter.php

# 4. Dodanie routingu
# config/routes.php

# 5. Utworzenie testów
# test/Handler/ProductHandlerTest.php
```

#### Testowanie
```bash
# Uruchomienie testów
composer test

# Sprawdzenie analizy statycznej
composer static-analysis

# Sprawdzenie standardów kodowania
composer cs-check
```

#### Dokumentacja
- [ ] Dodanie endpointów do README.md
- [ ] Aktualizacja TODO.md
- [ ] Dodanie do OpenAPI/Swagger

---

### Zadanie: Dodanie nowej funkcjonalności eksportu

#### Analiza wymagań
- [ ] Eksport do nowego formatu (CSV, JSON)
- [ ] Filtrowanie danych przed eksportem
- [ ] Obsługa dużych zbiorów danych
- [ ] Testy wydajnościowe

#### Planowanie
- [ ] Wybór biblioteki do eksportu
- [ ] Implementacja nowego handlera eksportu
- [ ] Dodanie opcji filtrowania
- [ ] Optymalizacja dla dużych zbiorów
- [ ] Testy wydajnościowe

#### Implementacja
```bash
# 1. Instalacja zależności
composer require league/csv

# 2. Utworzenie handlera eksportu
# src/Handler/CsvExportHandler.php

# 3. Dodanie routingu
# config/routes.php

# 4. Implementacja testów
# test/Handler/CsvExportHandlerTest.php
```

#### Testowanie
```bash
# Testy jednostkowe
composer test

# Testy wydajnościowe
php -d memory_limit=512M test/Performance/ExportTest.php

# Testy z dużymi zbiorami danych
php bin/test-export-performance.php
```

---

## 🔧 Komendy pomocnicze

### Inicjalizacja projektu
```bash
# Utworzenie nowego projektu Mezzio
composer create-project mezzio/mezzio-skeleton nazwa-projektu

# Przejście do katalogu
cd nazwa-projektu

# Włączenie trybu deweloperskiego
composer development-enable
```

### Instalacja zależności
```bash
# Dodanie nowej zależności
composer require nazwa-pakietu

# Dodanie zależności deweloperskiej
composer require --dev nazwa-pakietu

# Aktualizacja zależności
composer update
```

### Testowanie
```bash
# Uruchomienie wszystkich testów
composer test

# Testy z pokryciem
composer test-coverage

# Analiza statyczna
composer static-analysis

# Standardy kodowania
composer cs-check
composer cs-fix
```

### Serwer deweloperski
```bash
# Uruchomienie serwera
composer serve

# Sprawdzenie statusu
composer development-status

# Wyłączenie trybu deweloperskiego
composer development-disable
```

### Baza danych
```bash
# Inicjalizacja bazy danych
php bin/init-database.php

# Czyszczenie cache
composer clear-config-cache
```

---

## 📝 Checklist dla każdego zadania

### Przed rozpoczęciem
- [ ] Zrozumiałem wymagania
- [ ] Mam wszystkie potrzebne narzędzia
- [ ] Utworzyłem plan działania
- [ ] Sprawdziłem istniejący kod

### Podczas implementacji
- [ ] Implementuję krok po kroku
- [ ] Testuję po każdym kroku
- [ ] Commituję regularnie
- [ ] Dokumentuję zmiany

### Po implementacji
- [ ] Wszystkie testy przechodzą
- [ ] Kod jest zgodny ze standardami
- [ ] Dokumentacja jest aktualna
- [ ] Funkcjonalność działa poprawnie

### Przed przekazaniem
- [ ] Code review
- [ ] Testy end-to-end
- [ ] Sprawdzenie wydajności
- [ ] Aktualizacja TODO.md

---

## 🚨 Typowe problemy i rozwiązania

### Problem: Błędy w testach
```bash
# Sprawdź logi błędów
composer test --verbose

# Uruchom konkretny test
./vendor/bin/phpunit test/Handler/UserHandlerTest.php

# Sprawdź konfigurację PHPUnit
cat phpunit.xml.dist
```

### Problem: Błędy walidacji
```bash
# Sprawdź InputFilter
# Sprawdź czy wszystkie wymagane pola są zdefiniowane
# Sprawdź czy validation group jest poprawny
```

### Problem: Błędy routingu
```bash
# Sprawdź konfigurację routingu
cat config/routes.php

# Sprawdź czy handler jest zarejestrowany
cat config/autoload/dependencies.global.php
```

### Problem: Błędy bazy danych
```bash
# Sprawdź konfigurację Doctrine
cat config/autoload/doctrine.global.php

# Zainicjalizuj ponownie bazę danych
php bin/init-database.php

# Sprawdź uprawnienia
ls -la data/
```

---

## 📊 Metryki jakości

### Kod
- [ ] Pokrycie testami > 80%
- [ ] Brak błędów w analizie statycznej
- [ ] Zgodność ze standardami kodowania
- [ ] Brak duplikacji kodu

### Wydajność
- [ ] Czas odpowiedzi API < 500ms
- [ ] Użycie pamięci < 128MB
- [ ] Optymalne zapytania SQL
- [ ] Brak memory leaks

### Bezpieczeństwo
- [ ] Walidacja wszystkich danych wejściowych
- [ ] Parametryzowane zapytania SQL
- [ ] Brak XSS vulnerabilities
- [ ] Proper error handling

---

## 🎯 Szablon dla nowego zadania

### Zadanie: [NAZWA_ZADANIA]

#### Opis
[KRÓTKI_OPIS_ZADANIA]

#### Wymagania
- [ ] [WYMAGANIE_1]
- [ ] [WYMAGANIE_2]
- [ ] [WYMAGANIE_3]

#### Plan realizacji
1. [KROK_1]
2. [KROK_2]
3. [KROK_3]

#### Komendy do wykonania
```bash
# [KOMENDA_1]
# [KOMENDA_2]
# [KOMENDA_3]
```

#### Testy
- [ ] Testy jednostkowe
- [ ] Testy integracyjne
- [ ] Testy wydajnościowe
- [ ] Testy ręczne

#### Dokumentacja
- [ ] Aktualizacja README.md
- [ ] Aktualizacja TODO.md
- [ ] Dokumentacja API
- [ ] Komentarze w kodzie

#### Status
- [ ] Rozpoczęte
- [ ] W trakcie
- [ ] Testowanie
- [ ] Zakończone
