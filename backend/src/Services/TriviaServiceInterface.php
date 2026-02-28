<?php

declare(strict_types=1);

namespace App\Services;

interface TriviaServiceInterface
{
    public function getRandomClue(): array;
    public function getBatchClues(int $count): array;
    public function getCategories(): array;
    public function getCluesByCategory(string $category, int $count = 5): array;
    public function getCluesByDifficulty(string $difficulty): array;
    public function getServiceName(): string;
}
