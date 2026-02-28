<?php

declare(strict_types=1);

use App\Controllers\ClueController;
use App\Controllers\CategoryController;

return [
    'GET' => [
        '/api/clues/random'               => [ClueController::class, 'random'],
        '/api/clues/batch'                => [ClueController::class, 'batch'],
        '/api/clues/category/:categoryId' => [ClueController::class, 'category'],
        '/api/categories'                 => [CategoryController::class, 'index'],
    ],
];
