<?php

declare(strict_types=1);

namespace App\Services;

use Exception;

class OpenTriviaService extends BaseTriviaService
{
    private const API_BASE = 'https://opentdb.com';

    public function getRandomClue(): array
    {
        $token    = $this->getOrCreateToken();
        $response = $this->fetchWithRetry(self::API_BASE . "/api.php?amount=1&type=multiple&token=$token");

        return $this->normalizeClue($response['results'][0]);
    }

    public function getBatchClues(int $count): array
    {
        $token    = $this->getOrCreateToken();
        $response = $this->fetchWithRetry(self::API_BASE . "/api.php?amount=$count&type=multiple&token=$token");

        return array_map(fn($q) => $this->normalizeClue($q), $response['results']);
    }

    public function getCategories(): array
    {
        return [
            ['id' => 9,  'name' => 'General Knowledge'],
            ['id' => 10, 'name' => 'Books'],
            ['id' => 11, 'name' => 'Film'],
            ['id' => 12, 'name' => 'Music'],
            ['id' => 13, 'name' => 'Musicals & Theatres'],
            ['id' => 14, 'name' => 'Television'],
            ['id' => 15, 'name' => 'Video Games'],
            ['id' => 16, 'name' => 'Board Games'],
            ['id' => 17, 'name' => 'Science & Nature'],
            ['id' => 18, 'name' => 'Computers'],
            ['id' => 19, 'name' => 'Mathematics'],
            ['id' => 20, 'name' => 'Mythology'],
            ['id' => 21, 'name' => 'Sports'],
            ['id' => 22, 'name' => 'Geography'],
            ['id' => 23, 'name' => 'History'],
            ['id' => 24, 'name' => 'Politics'],
            ['id' => 25, 'name' => 'Art'],
            ['id' => 26, 'name' => 'Celebrities'],
            ['id' => 27, 'name' => 'Animals'],
            ['id' => 28, 'name' => 'Vehicles'],
            ['id' => 29, 'name' => 'Comics'],
            ['id' => 30, 'name' => 'Gadgets'],
            ['id' => 31, 'name' => 'Japanese Anime & Manga'],
            ['id' => 32, 'name' => 'Cartoon & Animations'],
        ];
    }

    public function getCluesByCategory(string $category, int $count = 5): array
    {
        if (!is_numeric($category)) {
            throw new Exception('Category ID must be numeric');
        }

        $token    = $this->getOrCreateToken();
        $response = $this->fetchWithRetry(
            self::API_BASE . "/api.php?amount=$count&category=$category&type=multiple&token=$token"
        );

        return array_map(fn($q) => $this->normalizeClue($q), $response['results']);
    }

    public function getCluesByDifficulty(string $difficulty, int $count = 50): array
    {
        if (!in_array($difficulty, ['easy', 'medium', 'hard'])) {
            throw new Exception("Invalid difficulty: $difficulty");
        }

        $token    = $this->getOrCreateToken();
        $response = $this->fetchWithRetry(
            self::API_BASE . "/api.php?amount=$count&difficulty=$difficulty&type=multiple&token=$token"
        );

        return array_map(fn($q) => $this->normalizeClue($q), $response['results']);
    }

    public function getBoardClues(int $categoryCount = 6, int $cluesPerCategory = 5): array
    {
        $token  = $this->getOrCreateToken();
        $batch1 = $this->fetchWithRetry(self::API_BASE . "/api.php?amount=50&type=multiple&token=$token");
        $batch2 = $this->fetchWithRetry(self::API_BASE . "/api.php?amount=50&type=multiple&token=$token");

        $byCategory = [];
        foreach (array_merge($batch1['results'], $batch2['results']) as $question) {
            $cat                 = $this->stripCategoryPrefix($this->decodeHtml($question['category'] ?? ''));
            $byCategory[$cat][] = $question;
        }

        $eligible = array_filter($byCategory, fn($clues) => count($clues) >= $cluesPerCategory);

        if (count($eligible) < $categoryCount) {
            throw new Exception("Not enough categories with {$cluesPerCategory}+ questions to build a {$categoryCount}-category board.");
        }

        $keys = array_keys($eligible);
        shuffle($keys);

        $board = [];
        foreach (array_slice($keys, 0, $categoryCount) as $catName) {
            $board[] = [
                'category' => $catName,
                'clues'    => array_map(fn($q) => $this->normalizeClue($q), array_slice($eligible[$catName], 0, $cluesPerCategory)),
            ];
        }

        return $board;
    }

    public function getServiceName(): string
    {
        return 'Open Trivia Database';
    }

    protected function normalizeClue(array $rawData): array
    {
        return [
            'category'   => $this->stripCategoryPrefix($this->decodeHtml($rawData['category'] ?? 'General Knowledge')),
            'clue'       => $this->decodeHtml($rawData['question'] ?? ''),
            'response'   => $this->decodeHtml($rawData['correct_answer'] ?? ''),
            'difficulty' => $rawData['difficulty'] ?? 'medium',
            'type'       => $rawData['type'] ?? 'multiple',
            'source'     => $this->getServiceName(),
        ];
    }

    private function stripCategoryPrefix(string $category): string
    {
        return (string) preg_replace('/^(?:Entertainment|Science):\s*/', '', $category);
    }

    private function getOrCreateToken(): string
    {
        if (!isset($_SESSION['opentdb_token'])) {
            $response = $this->makeHttpRequest(self::API_BASE . '/api_token.php?command=request');

            if (($response['response_code'] ?? -1) !== 0) {
                throw new Exception('Failed to obtain session token');
            }

            $_SESSION['opentdb_token'] = $response['token'];
        }

        return $_SESSION['opentdb_token'];
    }

    private function resetSessionToken(): void
    {
        $token = $_SESSION['opentdb_token'] ?? null;

        if ($token === null) {
            return;
        }

        // Token value is unchanged after reset; the seen-questions list is cleared server-side
        $this->makeHttpRequest(self::API_BASE . "/api_token.php?command=reset&token=$token");
    }

    private function fetchWithRetry(string $url, int $maxAttempts = 3, int $retryDelay = 5): array
    {
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $response = $this->makeHttpRequest($url);
            $code     = $response['response_code'] ?? -1;

            if ($code === 5) {
                if ($attempt < $maxAttempts) {
                    sleep($retryDelay);
                    continue;
                }
                throw new Exception('Rate limit exceeded. Please try again in a moment.');
            }

            if ($code === 4) {
                $this->resetSessionToken();
                if ($attempt < $maxAttempts) {
                    continue;
                }
                throw new Exception('All available questions have been seen. Session has been reset — please try again.');
            }

            $this->assertValidResponse($response);
            return $response;
        }

        throw new Exception('Request failed after maximum retries.');
    }

    private function assertValidResponse(array $response): void
    {
        $errors = [1 => 'No results', 2 => 'Invalid parameter', 3 => 'Token not found'];
        $code   = $response['response_code'] ?? -1;

        if ($code !== 0) {
            throw new Exception($errors[$code] ?? 'Unknown API error');
        }

        if (empty($response['results'])) {
            throw new Exception('No questions returned');
        }
    }
}
