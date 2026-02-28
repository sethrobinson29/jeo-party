<?php

declare(strict_types=1);

namespace App\Services;

class OpenTriviaService extends BaseTriviaService
{
    private const API_BASE = 'https://opentdb.com';

    public function getRandomClue(): array
    {
        $response = $this->makeHttpRequest(self::API_BASE . '/api.php?amount=1');
        $this->assertValidResponse($response);

        return $this->normalizeClue($response['results'][0]);
    }

    public function getCluesByCategory(string $category): array
    {
        throw new \Exception('Category filtering not implemented for Open Trivia DB');
    }

    public function getCluesByDifficulty(string $difficulty): array
    {
        if (!in_array($difficulty, ['easy', 'medium', 'hard'])) {
            throw new \Exception("Invalid difficulty: $difficulty");
        }

        $response = $this->makeHttpRequest(self::API_BASE . "/api.php?amount=10&difficulty=$difficulty");
        $this->assertValidResponse($response);

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

    private function assertValidResponse(array $response): void
    {
        $errors = [1 => 'No results', 2 => 'Invalid parameter', 3 => 'Token not found', 4 => 'Token empty', 5 => 'Rate limited'];
        $code = $response['response_code'] ?? -1;

        if ($code !== 0) {
            throw new \Exception($errors[$code] ?? 'Unknown API error');
        }

        if (empty($response['results'])) {
            throw new \Exception('No questions returned');
        }
    }
}
