<?php

declare(strict_types=1);

/**
 * Application Routes Configuration
 *
 * Define all HTTP routes here
 * Format: 'METHOD' => ['/path' => [ControllerClass::class, 'methodName']]
 */

use App\Controllers\ClueController;

return [
    'GET' => [
        // Trivia clue endpoints
        '/api/clues/random' => [ClueController::class, 'random'],

        // Future endpoints
        // '/api/clues/category/:category' => [ClueController::class, 'byCategory'],
        // '/api/clues/difficulty/:difficulty' => [ClueController::class, 'byDifficulty'],
    ],

    'POST' => [
        // Future POST endpoints
        // '/api/clues/validate' => [ClueController::class, 'validateAnswer'],
    ],

    'PUT' => [
        // Future PUT endpoints
    ],

    'DELETE' => [
        // Future DELETE endpoints
    ]
];