<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Custom Trivia Service (Example)
 *
 * Template for implementing your own trivia database/API
 * This shows how to create a new service that implements the interface
 */
class CustomTriviaService extends BaseTriviaService
{
    private const API_BASE_URL = 'https://your-api.com'; // Your API URL
    private string $apiKey;

    public function __construct(?string $apiKey = null)
    {
        // Load API key from environment or config
        $this->apiKey = $apiKey ?? $_ENV['CUSTOM_API_KEY'] ?? '';
    }

    /**
     * @inheritDoc
     */
    public function getRandomClue(): array
    {
        $url = self::API_BASE_URL . '/random';

        // Add authentication header if needed
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->apiKey}",
            'Accept: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \Exception("API request failed with status: $httpCode");
        }

        $data = json_decode($response, true);

        return $this->normalizeClue($data);
    }

    /**
     * @inheritDoc
     */
    public function getCluesByCategory(string $category): array
    {
        $url = self::API_BASE_URL . "/category/" . urlencode($category);
        $response = $this->makeHttpRequest($url);

        // Assuming response is an array of questions
        return array_map(
            fn($item) => $this->normalizeClue($item),
            $response['questions'] ?? []
        );
    }

    /**
     * @inheritDoc
     */
    public function getCluesByDifficulty(string $difficulty): array
    {
        $url = self::API_BASE_URL . "/difficulty/" . urlencode($difficulty);
        $response = $this->makeHttpRequest($url);

        return array_map(
            fn($item) => $this->normalizeClue($item),
            $response['questions'] ?? []
        );
    }

    /**
     * @inheritDoc
     */
    public function getServiceName(): string
    {
        return 'Custom Trivia Service';
    }

    /**
     * @inheritDoc
     */
    protected function normalizeClue(array $rawData): array
    {
        // Map your API's field names to the standard format
        return [
            'category' => $rawData['cat'] ?? 'Unknown',
            'clue' => $rawData['q'] ?? '',
            'response' => $rawData['a'] ?? '',
            'difficulty' => $rawData['diff'] ?? 'medium',
            'type' => $rawData['t'] ?? 'text',
            'source' => $this->getServiceName()
        ];
    }
}