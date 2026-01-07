<?php

declare(strict_types=1);

namespace App\Services;

use Exception;

/**
 * Open Trivia DB Service
 *
 * Implementation for Open Trivia Database API
 * API Documentation: https://opentdb.com/api_config.php
 */
class OpenTriviaService extends BaseTriviaService
{
    private const API_BASE_URL = 'https://opentdb.com';

    /**
     * @inheritDoc
     */
    public function getRandomClue(): array
    {
        $url = self::API_BASE_URL . '/api.php?amount=1';
        $response = $this->makeHttpRequest($url);

        $this->validateResponse($response);

        $question = $response['results'][0] ?? null;

        if (!$question) {
            throw new Exception('No question data returned');
        }

        return $this->normalizeClue($question);
    }

    /**
     * @inheritDoc
     */
    public function getCluesByCategory(string $category): array
    {
        // Open Trivia DB uses numeric category IDs
        // This is a simplified implementation
        // In production, map category names to IDs

        throw new Exception('Category filtering not yet implemented for Open Trivia DB');
    }

    /**
     * @inheritDoc
     */
    public function getCluesByDifficulty(string $difficulty): array
    {
        $validDifficulties = ['easy', 'medium', 'hard'];

        if (!in_array($difficulty, $validDifficulties)) {
            throw new Exception("Invalid difficulty: $difficulty");
        }

        $url = self::API_BASE_URL . "/api.php?amount=1&difficulty=$difficulty";
        $response = $this->makeHttpRequest($url);

        $this->validateResponse($response);

        return array_map(
            fn($q) => $this->normalizeClue($q),
            $response['results'] ?? []
        );
    }

    /**
     * @inheritDoc
     */
    public function getServiceName(): string
    {
        return 'Open Trivia Database';
    }

    /**
     * Validate Open Trivia DB response
     *
     * @throws Exception if response is invalid
     */
    private function validateResponse(array $response): void
    {
        if (!isset($response['response_code'])) {
            throw new Exception('Invalid API response structure');
        }

        $errorMessages = [
            0 => null, // Success
            1 => 'No results found',
            2 => 'Invalid parameter',
            3 => 'Token not found',
            4 => 'Token empty',
            5 => 'Rate limit exceeded'
        ];

        $code = $response['response_code'];

        if ($code !== 0) {
            $message = $errorMessages[$code] ?? 'Unknown error';
            throw new Exception($message);
        }

        if (empty($response['results'])) {
            throw new Exception('No questions returned');
        }
    }

    /**
     * @inheritDoc
     */
    protected function normalizeClue(array $rawData): array
    {
        return [
            'category' => $this->decodeHtml($rawData['category'] ?? 'General Knowledge'),
            'clue' => $this->decodeHtml($rawData['question'] ?? ''),
            'response' => $this->decodeHtml($rawData['correct_answer'] ?? ''),
            'difficulty' => $rawData['difficulty'] ?? 'medium',
            'type' => $rawData['type'] ?? 'multiple',
            'source' => $this->getServiceName()
        ];
    }
}