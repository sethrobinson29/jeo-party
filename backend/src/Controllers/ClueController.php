<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\TriviaServiceInterface;
use App\Services\OpenTriviaService;

/**
 * Clue Controller
 *
 * Handles trivia clue-related requests
 */
class ClueController extends BaseController
{
    private TriviaServiceInterface $triviaService;

    public function __construct()
    {
        // In the future, inject this via DI container
        // For now, instantiate the default service
        $this->triviaService = new OpenTriviaService();
    }

    /**
     * GET /api/clues/random
     *
     * Fetch a random trivia clue
     */
    public function random(Request $request): Response
    {
        try {
            $clue = $this->triviaService->getRandomClue();

            return Response::success(['clue' => $clue]);
        } catch (\Exception $e) {
            return Response::error(
                'Failed to fetch clue',
                500,
                ['message' => $e->getMessage()]
            );
        }
    }

    /**
     * GET /api/clues/category/:category
     *
     * Fetch clues by category (example of future endpoint)
     */
    public function byCategory(Request $request): Response
    {
        $category = $this->getRouteParam($request, 'category');

        try {
            $clues = $this->triviaService->getCluesByCategory($category);

            return Response::success(['clues' => $clues]);
        } catch (\Exception $e) {
            return Response::error(
                'Failed to fetch clues',
                500,
                ['message' => $e->getMessage()]
            );
        }
    }
}