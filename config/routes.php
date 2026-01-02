<?php

/**
 * Route Format:
 * 'METHOD' => [
 *     '/path' => ['ControllerClass', 'methodName']
 * ]
 */

return [
    'GET' => [
        '/api/clues/random' => ['Controllers\ClueController', 'random'],
    ],
];