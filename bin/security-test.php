#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Security Test Script
 * 
 * Ten skrypt testuje różne aspekty bezpieczeństwa aplikacji:
 * - SQL Injection
 * - XSS (Cross-Site Scripting)
 * - Walidacja danych
 * - Nagłówki HTTP
 * - Autoryzacja
 * - Rate limiting
 * - Logowanie błędów
 */

class SecurityTester
{
    private string $baseUrl;
    private array $results = [];
    private int $passed = 0;
    private int $failed = 0;

    public function __construct(string $baseUrl = 'http://localhost:8080')
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function runAllTests(): void
    {
        echo "🔒 Uruchamianie testów bezpieczeństwa...\n";
        echo "URL: {$this->baseUrl}\n";
        echo str_repeat("=", 50) . "\n\n";

        $this->testSqlInjection();
        $this->testXss();
        $this->testDataValidation();
        $this->testHttpHeaders();
        $this->testAuthorization();
        $this->testRateLimiting();
        $this->testErrorHandling();
        $this->testInputFiltering();

        $this->printResults();
    }

    private function testSqlInjection(): void
    {
        echo "🧪 Testy SQL Injection...\n";
        
        $payloads = [
            "' OR 1=1 --",
            "'; DROP TABLE user; --",
            "' UNION SELECT * FROM user --",
            "admin'--",
            "1' OR '1'='1",
        ];

        foreach ($payloads as $payload) {
            $this->testEndpoint("GET", "/api/users?name=" . urlencode($payload), "SQL Injection: $payload");
        }

        // Test POST data
        $this->testEndpoint("POST", "/api/users", "SQL Injection POST", [
            'name' => "' OR 1=1 --",
            'phone_number' => "'; DROP TABLE user; --",
            'address' => "test",
            'age' => 25
        ]);

        echo "\n";
    }

    private function testXss(): void
    {
        echo "🧪 Testy XSS (Cross-Site Scripting)...\n";
        
        $payloads = [
            '<script>alert("XSS")</script>',
            '<img src=x onerror=alert("XSS")>',
            'javascript:alert("XSS")',
            '<svg onload=alert("XSS")>',
            '"><script>alert("XSS")</script>',
        ];

        foreach ($payloads as $payload) {
            $this->testEndpoint("POST", "/api/users", "XSS: $payload", [
                'name' => $payload,
                'phone_number' => '123456789',
                'address' => 'test',
                'age' => 25
            ]);
        }

        echo "\n";
    }

    private function testDataValidation(): void
    {
        echo "🧪 Testy walidacji danych...\n";
        
        // Test nieprawidłowych typów danych
        $this->testEndpoint("POST", "/api/users", "Invalid age type", [
            'name' => 'Test User',
            'phone_number' => '123456789',
            'address' => 'test',
            'age' => 'not-a-number'
        ]);

        // Test zbyt długich danych
        $this->testEndpoint("POST", "/api/users", "Too long name", [
            'name' => str_repeat('A', 1000),
            'phone_number' => '123456789',
            'address' => 'test',
            'age' => 25
        ]);

        // Test pustych danych
        $this->testEndpoint("POST", "/api/users", "Empty data", []);

        // Test nieprawidłowego wieku
        $this->testEndpoint("POST", "/api/users", "Invalid age range", [
            'name' => 'Test User',
            'phone_number' => '123456789',
            'address' => 'test',
            'age' => 200
        ]);

        echo "\n";
    }

    private function testHttpHeaders(): void
    {
        echo "🧪 Testy nagłówków HTTP...\n";
        
        $response = $this->makeRequest("GET", "/api/users");
        
        if ($response) {
            $headers = $response['headers'];
            
            $securityHeaders = [
                'X-Content-Type-Options' => 'nosniff',
                'X-Frame-Options' => 'DENY',
                'X-XSS-Protection' => '1; mode=block',
                'Content-Security-Policy' => 'default-src \'self\'',
                'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
            ];

            foreach ($securityHeaders as $header => $expectedValue) {
                $this->testHeader($header, $expectedValue, $headers);
            }
        }

        echo "\n";
    }

    private function testAuthorization(): void
    {
        echo "🧪 Testy autoryzacji...\n";
        
        // Test dostępu bez autoryzacji (powinien działać w tej aplikacji)
        $this->testEndpoint("GET", "/api/users", "Access without authorization");
        
        // Test z nieprawidłowym tokenem
        $this->testEndpoint("GET", "/api/users", "Invalid token", null, [
            'Authorization: Bearer invalid-token'
        ]);

        echo "\n";
    }

    private function testRateLimiting(): void
    {
        echo "🧪 Testy rate limiting...\n";
        
        // Wysyłanie wielu żądań
        $responses = [];
        for ($i = 0; $i < 10; $i++) {
            $response = $this->makeRequest("GET", "/api/users");
            $responses[] = $response;
            usleep(100000); // 0.1 sekundy
        }

        // Sprawdzenie czy wszystkie żądania przeszły (brak rate limiting w tej aplikacji)
        $successCount = 0;
        foreach ($responses as $response) {
            if ($response && $response['status'] < 400) {
                $successCount++;
            }
        }

        $this->addResult("Rate Limiting", $successCount === 10, 
            "Wszystkie żądania przeszły (brak rate limiting)", 
            "Niektóre żądania zostały zablokowane");

        echo "\n";
    }

    private function testErrorHandling(): void
    {
        echo "🧪 Testy obsługi błędów...\n";
        
        // Test nieistniejącego endpointu
        $this->testEndpoint("GET", "/api/nonexistent", "Non-existent endpoint");
        
        // Test nieprawidłowej metody HTTP
        $this->testEndpoint("PATCH", "/api/users", "Invalid HTTP method");
        
        // Test nieprawidłowego JSON
        $this->testEndpoint("POST", "/api/users", "Invalid JSON", "invalid json", [
            'Content-Type: application/json'
        ]);

        echo "\n";
    }

    private function testInputFiltering(): void
    {
        echo "🧪 Testy filtrowania danych wejściowych...\n";
        
        // Test specjalnych znaków
        $this->testEndpoint("POST", "/api/users", "Special characters", [
            'name' => 'Test\'User"<>&',
            'phone_number' => '+48 123-456-789',
            'address' => 'ul. Testowa 1/2, 00-000 Warszawa',
            'age' => 25
        ]);

        // Test Unicode
        $this->testEndpoint("POST", "/api/users", "Unicode characters", [
            'name' => 'Józef Łódź',
            'phone_number' => '123456789',
            'address' => 'ul. Świętokrzyska 1, Warszawa',
            'age' => 25
        ]);

        echo "\n";
    }

    private function testEndpoint(string $method, string $endpoint, string $testName, $data = null, array $headers = []): void
    {
        $response = $this->makeRequest($method, $endpoint, $data, $headers);
        
        if ($response) {
            $status = $response['status'];
            $body = $response['body'];
            
            // Sprawdzenie czy odpowiedź jest bezpieczna
            $isSecure = $this->isResponseSecure($status, $body, $testName);
            
            $this->addResult($testName, $isSecure, 
                "Odpowiedź jest bezpieczna (status: $status)", 
                "Odpowiedź może być niebezpieczna (status: $status)");
        } else {
            $this->addResult($testName, false, "Błąd połączenia", "Nie udało się połączyć z serwerem");
        }
    }

    private function testHeader(string $headerName, string $expectedValue, array $headers): void
    {
        $headerValue = $headers[$headerName] ?? null;
        $hasHeader = $headerValue !== null;
        
        $this->addResult("Header: $headerName", $hasHeader, 
            "Nagłówek $headerName jest ustawiony: $headerValue", 
            "Brak nagłówka $headerName (oczekiwano: $expectedValue)");
    }

    private function makeRequest(string $method, string $endpoint, $data = null, array $headers = []): ?array
    {
        $url = $this->baseUrl . $endpoint;
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => false,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => array_merge([
                'User-Agent: SecurityTester/1.0',
                'Accept: application/json',
            ], $headers)
        ]);

        if ($method === 'POST' || $method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if ($data !== null) {
                if (is_array($data)) {
                    $data = json_encode($data);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge([
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($data)
                    ], $headers));
                }
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return null;
        }

        // Parsowanie odpowiedzi
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headerText = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);

        // Parsowanie nagłówków
        $headers = [];
        foreach (explode("\n", $headerText) as $line) {
            if (strpos($line, ':') !== false) {
                list($name, $value) = explode(':', $line, 2);
                $headers[trim($name)] = trim($value);
            }
        }

        return [
            'status' => $httpCode,
            'headers' => $headers,
            'body' => $body
        ];
    }

    private function isResponseSecure(int $status, string $body, string $testName): bool
    {
        // Sprawdzenie czy odpowiedź zawiera błędy SQL
        if (stripos($body, 'sql') !== false && stripos($body, 'error') !== false) {
            return false;
        }

        // Sprawdzenie czy odpowiedź zawiera błędy PHP
        if (stripos($body, 'fatal error') !== false || stripos($body, 'parse error') !== false) {
            return false;
        }

        // Sprawdzenie czy odpowiedź ujawnia wrażliwe informacje
        if (stripos($body, 'database') !== false && stripos($body, 'password') !== false) {
            return false;
        }

        // Sprawdzenie czy odpowiedź zawiera wykonany kod JavaScript
        if (stripos($body, '<script>') !== false && stripos($body, 'alert') !== false) {
            return false;
        }

        // Dla testów SQL Injection - sprawdzenie czy zwracany jest błąd 400/422
        if (strpos($testName, 'SQL Injection') !== false) {
            return $status >= 400; // Oczekujemy błędu walidacji
        }

        // Dla testów XSS - sprawdzenie czy kod nie jest wykonywany
        if (strpos($testName, 'XSS') !== false) {
            return $status >= 400 || stripos($body, '<script>') === false;
        }

        return true;
    }

    private function addResult(string $testName, bool $passed, string $successMessage, string $failureMessage): void
    {
        $this->results[] = [
            'name' => $testName,
            'passed' => $passed,
            'message' => $passed ? $successMessage : $failureMessage
        ];

        if ($passed) {
            $this->passed++;
            echo "✅ $testName: $successMessage\n";
        } else {
            $this->failed++;
            echo "❌ $testName: $failureMessage\n";
        }
    }

    private function printResults(): void
    {
        echo str_repeat("=", 50) . "\n";
        echo "📊 WYNIKI TESTÓW BEZPIECZEŃSTWA\n";
        echo str_repeat("=", 50) . "\n";
        echo "✅ Przeszło: {$this->passed}\n";
        echo "❌ Nie przeszło: {$this->failed}\n";
        echo "📈 Wskaźnik sukcesu: " . round(($this->passed / ($this->passed + $this->failed)) * 100, 1) . "%\n\n";

        if ($this->failed > 0) {
            echo "🚨 PROBLEMY DO NAPRAWY:\n";
            foreach ($this->results as $result) {
                if (!$result['passed']) {
                    echo "  - {$result['name']}: {$result['message']}\n";
                }
            }
            echo "\n";
        }

        echo "💡 REKOMENDACJE:\n";
        if ($this->failed === 0) {
            echo "  - Aplikacja przechodzi wszystkie testy bezpieczeństwa!\n";
            echo "  - Rozważ dodanie dodatkowych warstw bezpieczeństwa\n";
        } else {
            echo "  - Napraw zidentyfikowane problemy bezpieczeństwa\n";
            echo "  - Dodaj walidację danych wejściowych\n";
            echo "  - Zaimplementuj nagłówki bezpieczeństwa\n";
            echo "  - Rozważ dodanie rate limiting\n";
        }

        echo "\n🔗 Przydatne linki:\n";
        echo "  - OWASP Top 10: https://owasp.org/www-project-top-ten/\n";
        echo "  - OWASP Testing Guide: https://owasp.org/www-project-web-security-testing-guide/\n";
        echo "  - PHP Security: https://www.php.net/manual/en/security.php\n";
    }
}

// Uruchomienie testów
if (php_sapi_name() === 'cli') {
    $baseUrl = $argv[1] ?? 'http://localhost:8080';
    $tester = new SecurityTester($baseUrl);
    $tester->runAllTests();
} 