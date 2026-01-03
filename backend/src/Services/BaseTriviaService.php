<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Base Trivia Service
 *
 * Provides common functionality for all trivia services
 */
abstract class BaseTriviaService implements TriviaServiceInterface
{
    /**
     * Make an HTTP request
     *
     * @param string $url Full URL to request
     * @param string $method HTTP method (GET, POST, etc.)
     * @param array|null $data Request data for POST/PUT
     * @return array Response data
     * @throws \Exception if request fails
     */
    protected function makeHttpRequest(
        string $url,
        string $method = 'GET',
        ?array $data = null
    ): array {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Accept: application/json'
        ]);

        if ($method !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if ($data !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge(
                    curl_getopt($ch, CURLOPT_HTTPHEADER) ?? [],
                    ['Content-Type: application/json']
                ));
            }
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new \Exception("Connection error: $error");
        }

        if ($httpCode !== 200) {
            throw new \Exception("API returned status code: $httpCode");
        }

        $decoded = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON response from API');
        }

        return $decoded;
    }

    /**
     * Decode HTML entities
     *
     * @param string $text Text to decode
     * @return string Decoded text
     */
    protected function decodeHtml(string $text): string
    {
        return html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Normalize clue data to standard format
     *
     * @param array $rawData Raw data from API
     * @return array Normalized clue with keys: category, clue, response, difficulty, type
     */
    abstract protected function normalizeClue(array $rawData): array;
}