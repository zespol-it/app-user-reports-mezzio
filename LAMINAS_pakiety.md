# Laminas – Najpopularniejsze Pakiety

Poniżej znajduje się lista najczęściej używanych pakietów `laminas-*`, które są przydatne przy budowie aplikacji opartych na Laminas MVC lub Mezzio.

---

## 🧱 Struktura i MVC

| Pakiet | Opis |
|--------|------|
| `laminas/laminas-mvc` | Główna biblioteka do budowania aplikacji w stylu MVC. |
| `laminas/laminas-router` | Obsługa routingu (`Literal`, `Segment`, `Wildcard`). |
| `laminas/laminas-view` | System widoków z obsługą `.phtml`, layoutów i helperów. |
| `laminas/laminas-eventmanager` | Obsługa zdarzeń i hooków (pluginy, listenery). |
| `laminas/laminas-servicemanager` | Dependency Injection Container (DIC). |

---

## 💾 Dane i walidacja

| Pakiet | Opis |
|--------|------|
| `laminas/laminas-db` | Abstrakcja warstwy bazy danych (Zend\Db). |
| `laminas/laminas-validator` | Zestaw validatorów (np. `NotEmpty`, `EmailAddress`). |
| `laminas/laminas-filter` | Filtry danych (np. `StringTrim`, `StripTags`). |
| `laminas/laminas-inputfilter` | Walidacja struktur danych wejściowych (np. formularzy). |
| `laminas/laminas-hydrator` | Hydratory – konwersja obiektów ↔ tablice. |

---

## 🧾 Formularze

| Pakiet | Opis |
|--------|------|
| `laminas/laminas-form` | Tworzenie i obsługa formularzy HTML z walidacją i filtrami. |

---

## 🔐 Bezpieczeństwo / Sesje / Autoryzacja

| Pakiet | Opis |
|--------|------|
| `laminas/laminas-session` | Obsługa sesji (pliki, baza danych). |
| `laminas/laminas-authentication` | Logowanie użytkowników. |
| `laminas/laminas-permissions-acl` | Lista kontroli dostępu (ACL). |
| `laminas/laminas-crypt` | Operacje kryptograficzne (hashowanie, szyfrowanie). |

---

## 🌍 HTTP / Middleware / API

| Pakiet | Opis |
|--------|------|
| `laminas/laminas-diactoros` | PSR-7 HTTP Messages – fundament dla middleware. |
| `laminas/laminas-httphandlerrunner` | Uruchamianie PSR-15 middleware. |
| `laminas/laminas-json` | Obsługa JSON (z fallbackiem). |

---

## 🧪 Testowanie i deweloperka

| Pakiet | Opis |
|--------|------|
| `laminas/laminas-test` | Testowanie aplikacji MVC. |
| `laminas/laminas-developer-tools` | Panel debugowania dla Laminas MVC. |
| `laminas/laminas-config` | Obsługa konfiguracji z plików PHP, INI, XML, JSON. |

---

## 📦 Szybki start – `composer require`

```bash
composer require \
    laminas/laminas-mvc \
    laminas/laminas-router \
    laminas/laminas-view \
    laminas/laminas-db \
    laminas/laminas-validator \
    laminas/laminas-filter \
    laminas/laminas-form \
    laminas/laminas-session \
    laminas/laminas-authentication
