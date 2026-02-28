<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\LocalJeopardyService;
use App\Services\TriviaServiceInterface;

class ClueController extends BaseController
{
    private TriviaServiceInterface $triviaService;

    public function __construct()
    {
        $this->triviaService = new LocalJeopardyService();
    }

    public function randomAction(Request $request): Response
    {
        try {
            return Response::success(['clue' => $this->triviaService->getRandomClue()]);
        } catch (\Exception $e) {
            return Response::error('Failed to fetch clue', 500);
        }
    }
}
