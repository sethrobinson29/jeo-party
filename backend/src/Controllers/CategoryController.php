<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Logging\Logger;
use App\Services\OpenTriviaService;
use Throwable;

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
        } catch (Throwable $e) {
            Logger::error('Failed to fetch categories', ['exception' => $e]);
            return Response::error('Failed to fetch categories');
        }
    }
}
