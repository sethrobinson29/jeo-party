<?php

declare(strict_types=1);

namespace App\Services;

abstract class BaseTriviaService implements TriviaServiceInterface
{
    protected function makeHttpRequest(string $url, string $method = 'GET', ?array $data = null): array
    {
        $ch = curl_init();

        $headers = [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            'Accept: application/json',
        ];

        if ($method !== 'GET' && $data !== null) {
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER     => $headers,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new \Exception("Connection error: $error");
        }

        if ($httpCode !== 200 && $httpCode !== 429) {
            throw new \Exception("API returned status $httpCode");
        }

        $decoded = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON response');
        }

        return $decoded;
    }

    protected function decodeHtml(string $text): string
    {
        return html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    abstract protected function normalizeClue(array $rawData): array;
}
