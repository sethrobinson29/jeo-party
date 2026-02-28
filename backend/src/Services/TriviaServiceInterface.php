<?php

declare(strict_types=1);

namespace App\Services;

interface TriviaServiceInterface
{
    public function getRandomClue(): array;
    public function getCluesByCategory(string $category): array;
    public function getCluesByDifficulty(string $difficulty): array;
    public function getServiceName(): string;
}
