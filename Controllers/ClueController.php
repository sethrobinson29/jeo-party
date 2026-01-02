<?php

namespace Controllers;

use Models\ClueModel;

/**
 * Clue Controller
 * Handles all clue-related requests
 */
class ClueController
{
    private $model;

    public function __construct()
    {
        $this->model = new ClueModel();
    }

    /**
     * GET /api/clues/random
     * Fetches a random trivia clue
     */
    public function random()
    {
        try {
            $clue = $this->model->getRandomClue();

            if (!$clue) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to fetch clue'
                ]);
                return;
            }

            echo json_encode([
                'success' => true,
                'clue' => $clue
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}