<?php

namespace Controllers;

use Exception;
use Models\ClueModel;

class ClueController
{
    private ClueModel $model;

    public function __construct()
    {
        $this->model = new ClueModel();
    }

    public function random(): void
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
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function randomBatch(): void
    {
        //TODO
    }
}