<?php

namespace Models;

use Exception;
use Services\OpenTriviaService;

class ClueModel
{
    private OpenTriviaService $triviaService;

    public function __construct()
    {
        $this->triviaService = new OpenTriviaService();
    }

    /** @throws Exception */
    public function getRandomClue(): array|null
    {
        $result = $this->triviaService->fetchRandomQuestion();

        if (!$result['success']) {
            throw new Exception($result['error']);
        }

        $question = $result['data'][0] ?? null;

        if (!$question) {
            throw new Exception('No question data available');
        }

        $normalizedClue = $this->normalizeClueData($question);

        if (empty($normalizedClue['clue']) || empty($normalizedClue['response'])) {
            throw new Exception('Invalid question data');
        }

        return $normalizedClue;
    }

    private function normalizeClueData(array $question): array
    {
        return [
            'category' => $this->decodeHtml($question['category'] ?? 'General Knowledge'),
            'clue' => $this->decodeHtml($question['question'] ?? ''),
            'response' => $this->decodeHtml($question['correct_answer'] ?? ''),
            'difficulty' => $question['difficulty'] ?? 'medium',
            'type' => $question['type'] ?? 'multiple'
        ];
    }

    private function decodeHtml(string $text): string
    {
        return html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}