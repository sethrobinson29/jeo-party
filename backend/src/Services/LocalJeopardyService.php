<?php

declare(strict_types=1);

namespace App\Services;

use Exception;

/**
 * Local Jeopardy Service
 *
 * Reads Jeopardy questions from a local JSON file
 * No external API or database required
 */
class LocalJeopardyService extends BaseTriviaService
{
    private array $questions = [];
    private string $dataFile;

    /**
     * @throws Exception
     */
    public function __construct(?string $dataFile = null)
    {
        $this->dataFile = $dataFile ?? dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'questions.json';
        $this->loadQuestions();
    }

    /**
     * Load questions from JSON file
     * @throws Exception
     */
    private function loadQuestions(): void
    {
        if (!file_exists($this->dataFile)) {
            throw new Exception("Jeopardy data file not found: {$this->dataFile}");
        }

        $json = file_get_contents($this->dataFile);
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON in Jeopardy data file');
        }

        $this->questions = $data;
    }

    /**
     * @inheritDoc
     */
    public function getRandomClue(): array
    {
        if (empty($this->questions)) {
            throw new Exception('No questions available');
        }

        $randomQuestion = $this->questions[array_rand($this->questions)];

        return $this->normalizeClue($randomQuestion);
    }

    /**
     * @inheritDoc
     */
    public function getCluesByCategory(string $category): array
    {
        $filtered = array_filter($this->questions, function($q) use ($category) {
            $qCategory = $q['category'] ?? '';
            return stripos($qCategory, $category) !== false;
        });

        if (empty($filtered)) {
            throw new Exception("No questions found for category: $category");
        }

        return array_map(
            fn($q) => $this->normalizeClue($q),
            array_slice($filtered, 0, 10) // Return max 10
        );
    }

    /**
     * @inheritDoc
     */
    public function getCluesByDifficulty(string $difficulty): array
    {
        // Map value ranges to difficulty
        $ranges = [
            'easy' => [100, 400],
            'medium' => [400, 800],
            'hard' => [800, 2000]
        ];

        if (!isset($ranges[$difficulty])) {
            throw new Exception("Invalid difficulty: $difficulty");
        }

        [$min, $max] = $ranges[$difficulty];

        $filtered = array_filter($this->questions, function($q) use ($min, $max) {
            $value = $q['value'] ?? 0;
            return $value >= $min && $value <= $max;
        });

        if (empty($filtered)) {
            return [$this->getRandomClue()]; // Fallback
        }

        return array_map(
            fn($q) => $this->normalizeClue($q),
            array_slice($filtered, 0, 10)
        );
    }

    /**
     * @inheritDoc
     */
    public function getServiceName(): string
    {
        return 'Local Jeopardy Database';
    }

    /**
     * @inheritDoc
     */
    protected function normalizeClue(array $rawData): array
    {
        // Handle different possible formats
        return [
            'category' => $this->decodeHtml($rawData['category'] ?? 'Unknown'),
            'clue' => $this->decodeHtml($rawData['question'] ?? $rawData['clue'] ?? ''),
            'response' => $this->decodeHtml($rawData['answer'] ?? $rawData['response'] ?? ''),
            'difficulty' => $this->calculateDifficulty($rawData['value'] ?? 0),
            'type' => 'jeopardy',
            'value' => $rawData['value'] ?? 0,
            'airdate' => $rawData['air_date'] ?? $rawData['airdate'] ?? null,
            'source' => $this->getServiceName()
        ];
    }

    /**
     * Calculate difficulty based on question value
     */
    private function calculateDifficulty(int $value): string
    {
        if ($value <= 400) return 'easy';
        if ($value <= 800) return 'medium';
        return 'hard';
    }

    /**
     * Get total number of questions
     */
    public function getQuestionCount(): int
    {
        return count($this->questions);
    }

    /**
     * Search questions by keyword
     */
    public function searchQuestions(string $keyword, int $limit = 10): array
    {
        $filtered = array_filter($this->questions, function($q) use ($keyword) {
            $searchText = strtolower(
                ($q['question'] ?? '') . ' ' .
                ($q['answer'] ?? '') . ' ' .
                ($q['category'] ?? '')
            );
            return stripos($searchText, strtolower($keyword)) !== false;
        });

        return array_map(
            fn($q) => $this->normalizeClue($q),
            array_slice($filtered, 0, $limit)
        );
    }
}