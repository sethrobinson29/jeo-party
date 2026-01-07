<?php

declare(strict_types=1);

namespace App\Services;

use Exception;

/**
 * Trivia Service Interface
 *
 * Defines the contract that all trivia services must implement
 * This allows easy switching between different trivia APIs or databases
 */
interface TriviaServiceInterface
{
    /**
     * Get a random trivia clue
     *
     * @return array Normalized clue data with keys: category, clue, response, difficulty, type
     * @throws Exception if the request fails
     */
    public function getRandomClue(): array;

    /**
     * Get clues by category
     *
     * @param string $category Category name
     * @return array Array of normalized clue data
     * @throws Exception if the request fails
     */
    public function getCluesByCategory(string $category): array;

    /**
     * Get clues by difficulty
     *
     * @param string $difficulty Difficulty level (easy, medium, hard)
     * @return array Array of normalized clue data
     * @throws Exception if the request fails
     */
    public function getCluesByDifficulty(string $difficulty): array;

    /**
     * Get the service name
     *
     * @return string Name of the trivia service (e.g., "Open Trivia DB")
     */
    public function getServiceName(): string;
}