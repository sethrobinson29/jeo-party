<?php

namespace Models;

use Services\OpenTriviaService;

/**
 * Clue Model
 * Handles business logic for trivia clues
 */
class ClueModel
{
    private $triviaService;

    public function __construct()
    {
        $this->triviaService = new OpenTriviaService();
    }

    /**
     * Get a random trivia clue
     *
     * @return array|null Normalized clue data or null on failure
     */
    public function getRandomClue()
    {
        $result = $this->triviaService->fetchRandomQuestion();

        if (!$result['success']) {
            throw new \Exception($result['error']);
        }

        $question = $result['data'][0] ?? null;

        if (!$question) {
            throw new \Exception('No question data available');
        }

        // Normalize the data structure
        $normalizedClue = $this->normalizeClueData($question);

        // Validate required fields
        if (empty($normalizedClue['clue']) || empty($normalizedClue['response'])) {
            throw new \Exception('Invalid question data');
        }

        return $normalizedClue;
    }

    /**
     * Normalize clue data from the API to our standard format
     *
     * @param array $question Raw question data from API
     * @return array Normalized clue data
     */
    private function normalizeClueData($question)
    {
        return [
            'category' => $this->decodeHtml($question['category'] ?? 'General Knowledge'),
            'clue' => $this->decodeHtml($question['question'] ?? ''),
            'response' => $this->decodeHtml($question['correct_answer'] ?? ''),
            'difficulty' => $question['difficulty'] ?? 'medium',
            'type' => $question['type'] ?? 'multiple'
        ];
    }

    /**
     * Decode HTML entities from API responses
     *
     * @param string $text Text to decode
     * @return string Decoded text
     */
    private function decodeHtml($text)
    {
        return html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}