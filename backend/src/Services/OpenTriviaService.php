<?php

declare(strict_types=1);

namespace App\Services;

class OpenTriviaService extends BaseTriviaService
{
    private const API_BASE = 'https://opentdb.com';

    public function getRandomClue(): array
    {
        $response = $this->fetchWithRetry(self::API_BASE . '/api.php?amount=1');

        return $this->normalizeClue($response['results'][0]);
    }

    public function getBatchClues(int $count): array
    {
        $response = $this->fetchWithRetry(self::API_BASE . "/api.php?amount=$count");

        return array_map(fn($q) => $this->normalizeClue($q), $response['results']);
    }

    public function getCategories(): array
    {
        return [
            ['id' => 9,  'name' => 'General Knowledge'],
            ['id' => 10, 'name' => 'Entertainment: Books'],
            ['id' => 11, 'name' => 'Entertainment: Film'],
            ['id' => 12, 'name' => 'Entertainment: Music'],
            ['id' => 13, 'name' => 'Entertainment: Musicals & Theatres'],
            ['id' => 14, 'name' => 'Entertainment: Television'],
            ['id' => 15, 'name' => 'Entertainment: Video Games'],
            ['id' => 16, 'name' => 'Entertainment: Board Games'],
            ['id' => 17, 'name' => 'Science & Nature'],
            ['id' => 18, 'name' => 'Science: Computers'],
            ['id' => 19, 'name' => 'Science: Mathematics'],
            ['id' => 20, 'name' => 'Mythology'],
            ['id' => 21, 'name' => 'Sports'],
            ['id' => 22, 'name' => 'Geography'],
            ['id' => 23, 'name' => 'History'],
            ['id' => 24, 'name' => 'Politics'],
            ['id' => 25, 'name' => 'Art'],
            ['id' => 26, 'name' => 'Celebrities'],
            ['id' => 27, 'name' => 'Animals'],
            ['id' => 28, 'name' => 'Vehicles'],
            ['id' => 29, 'name' => 'Entertainment: Comics'],
            ['id' => 30, 'name' => 'Science: Gadgets'],
            ['id' => 31, 'name' => 'Entertainment: Japanese Anime & Manga'],
            ['id' => 32, 'name' => 'Entertainment: Cartoon & Animations'],
        ];
    }

    public function getCluesByCategory(string $category, int $count = 5): array
    {
        if (!is_numeric($category)) {
            throw new \Exception('Category ID must be numeric');
        }

        $response = $this->fetchWithRetry(
            self::API_BASE . "/api.php?amount=$count&category=$category"
        );

        return array_map(fn($q) => $this->normalizeClue($q), $response['results']);
    }

    public function getCluesByDifficulty(string $difficulty): array
    {
        if (!in_array($difficulty, ['easy', 'medium', 'hard'])) {
            throw new \Exception("Invalid difficulty: $difficulty");
        }

        $response = $this->fetchWithRetry(self::API_BASE . "/api.php?amount=10&difficulty=$difficulty");

        return array_map(fn($q) => $this->normalizeClue($q), $response['results']);
    }

    public function getServiceName(): string
    {
        return 'Open Trivia Database';
    }

    protected function normalizeClue(array $rawData): array
    {
        return [
            'category'   => $this->decodeHtml($rawData['category'] ?? 'General Knowledge'),
            'clue'       => $this->decodeHtml($rawData['question'] ?? ''),
            'response'   => $this->decodeHtml($rawData['correct_answer'] ?? ''),
            'difficulty' => $rawData['difficulty'] ?? 'medium',
            'type'       => $rawData['type'] ?? 'multiple',
            'source'     => $this->getServiceName(),
        ];
    }

    private function fetchWithRetry(string $url, int $maxAttempts = 3, int $retryDelay = 5): array
    {
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $response = $this->makeHttpRequest($url);

            if (($response['response_code'] ?? -1) === 5) {
                if ($attempt < $maxAttempts) {
                    sleep($retryDelay);
                    continue;
                }
                throw new \Exception('Rate limit exceeded. Please try again in a moment.');
            }

            $this->assertValidResponse($response);
            return $response;
        }

        throw new \Exception('Request failed after maximum retries.');
    }

    private function assertValidResponse(array $response): void
    {
        $errors = [1 => 'No results', 2 => 'Invalid parameter', 3 => 'Token not found', 4 => 'Token empty'];
        $code = $response['response_code'] ?? -1;

        if ($code !== 0) {
            throw new \Exception($errors[$code] ?? 'Unknown API error');
        }

        if (empty($response['results'])) {
            throw new \Exception('No questions returned');
        }
    }
}
