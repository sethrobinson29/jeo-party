<?php

declare(strict_types=1);

use App\Controllers\ClueController;

return [
    'GET' => [
        '/api/clues/random' => [ClueController::class, 'random'],
    ],
];
