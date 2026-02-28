<?php

declare(strict_types=1);

namespace App\Services;

class LocalJeopardyService extends BaseTriviaService
{
    private array $questions;

    public function __construct(?string $dataFile = null)
    {
        $path = $dataFile ?? dirname(__DIR__, 2) . '/data/questions.json';

        if (!file_exists($path)) {
            throw new \Exception("Data file not found: $path");
        }

        $this->questions = json_decode(file_get_contents($path), true)
            ?? throw new \Exception('Invalid JSON in data file');
    }

    public function getRandomClue(): array
    {
        if (empty($this->questions)) {
            throw new \Exception('No questions available');
        }

        return $this->normalizeClue($this->questions[array_rand($this->questions)]);
    }

    public function getCluesByCategory(string $category): array
    {
        $filtered = array_filter(
            $this->questions,
            fn($q) => stripos($q['category'] ?? '', $category) !== false
        );

        if (empty($filtered)) {
            throw new \Exception("No questions found for category: $category");
        }

        return array_map(fn($q) => $this->normalizeClue($q), array_slice($filtered, 0, 10));
    }

    public function getCluesByDifficulty(string $difficulty): array
    {
        $ranges = ['easy' => [100, 400], 'medium' => [401, 800], 'hard' => [801, PHP_INT_MAX]];

        if (!isset($ranges[$difficulty])) {
            throw new \Exception("Invalid difficulty: $difficulty");
        }

        [$min, $max] = $ranges[$difficulty];

        $filtered = array_filter(
            $this->questions,
            fn($q) => ($q['value'] ?? 0) >= $min && ($q['value'] ?? 0) <= $max
        );

        if (empty($filtered)) {
            return [$this->getRandomClue()];
        }

        return array_map(fn($q) => $this->normalizeClue($q), array_slice($filtered, 0, 10));
    }

    public function getServiceName(): string
    {
        return 'Local Jeopardy Database';
    }

    protected function normalizeClue(array $rawData): array
    {
        return [
            'category'   => $this->decodeHtml($rawData['category'] ?? 'Unknown'),
            'clue'       => $this->decodeHtml($rawData['question'] ?? $rawData['clue'] ?? ''),
            'response'   => $this->decodeHtml($rawData['answer'] ?? $rawData['response'] ?? ''),
            'difficulty' => $this->inferDifficulty($rawData['value'] ?? 0),
            'value'      => $rawData['value'] ?? 0,
            'airdate'    => $rawData['air_date'] ?? $rawData['airdate'] ?? null,
            'type'       => 'jeopardy',
            'source'     => $this->getServiceName(),
        ];
    }

    private function inferDifficulty(int $value): string
    {
        return match (true) {
            $value <= 400 => 'easy',
            $value <= 800 => 'medium',
            default       => 'hard',
        };
    }
}
