<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\OpenTriviaService;

class CategoryController extends BaseController
{
    private OpenTriviaService $triviaService;

    public function __construct()
    {
        $this->triviaService = new OpenTriviaService();
    }

    public function indexAction(Request $request): Response
    {
        try {
            return Response::success(['categories' => $this->triviaService->getCategories()]);
        } catch (\Exception $e) {
            return Response::error('Failed to fetch categories', 500);
        }
    }
}
