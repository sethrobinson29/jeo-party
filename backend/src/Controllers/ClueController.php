<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Logging\Logger;
use App\Services\OpenTriviaService;
use App\Services\TriviaServiceInterface;

class ClueController extends BaseController
{
    private TriviaServiceInterface $triviaService;

    public function __construct()
    {
        $this->triviaService = new OpenTriviaService();
    }

    public function randomAction(Request $request): Response
    {
        try {
            return Response::success(['clue' => $this->triviaService->getRandomClue()]);
        } catch (\Exception $e) {
            Logger::error('Failed to fetch random clue', ['exception' => $e]);
            return Response::error('Failed to fetch clue', 500);
        }
    }

    public function batchAction(Request $request): Response
    {
        $count = (int) ($request->getQuery('count') ?? 61);
        $count = max(1, min($count, 100));

        try {
            return Response::success(['clues' => $this->triviaService->getBatchClues($count)]);
        } catch (\Exception $e) {
            Logger::error('Failed to fetch batch clues', ['exception' => $e, 'count' => $count]);
            return Response::error('Failed to fetch clues', 500);
        }
    }

    public function categoryAction(Request $request): Response
    {
        $categoryId = $this->getRouteParam($request, 'categoryId');

        if (!$categoryId || !is_numeric($categoryId)) {
            return Response::error('Invalid category ID', 400);
        }

        $count = (int) ($request->getQuery('count') ?? 5);
        $count = max(1, min($count, 50));

        try {
            return Response::success(['clues' => $this->triviaService->getCluesByCategory($categoryId, $count)]);
        } catch (\Exception $e) {
            Logger::error('Failed to fetch clues for category', ['exception' => $e, 'category' => $categoryId]);
            return Response::error('Failed to fetch clues for category', 500);
        }
    }
}
