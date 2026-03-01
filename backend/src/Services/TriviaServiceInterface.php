<?php

declare(strict_types=1);

namespace App\Services;

interface TriviaServiceInterface
{
    public function getRandomClue(): array;
    public function getBatchClues(int $count): array;
    public function getCategories(): array;
    public function getCluesByCategory(string $category, int $count = 5): array;
    public function getCluesByDifficulty(string $difficulty, int $count = 50): array;
    public function getBoardClues(int $categoryCount = 6, int $cluesPerCategory = 5): array;
    public function getServiceName(): string;
}
