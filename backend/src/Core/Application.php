<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Router;

/**
 * Main Application Class
 *
 * Bootstraps the application and coordinates request handling
 */
class Application
{
    private Router $router;

    public function __construct()
    {
        $this->router = new Router();
        $this->loadRoutes();
    }

    /**
     * Load application routes
     */
    private function loadRoutes(): void
    {
        $routes = require __DIR__ . '/../../config/routes.php';

        foreach ($routes as $method => $methodRoutes) {
            foreach ($methodRoutes as $path => $handler) {
                $this->router->addRoute($method, $path, $handler);
            }
        }
    }

    /**
     * Handle incoming request
     */
    public function handle(Request $request): Response
    {
        try {
            return $this->router->dispatch($request);
        } catch (\Exception $e) {
            return Response::error(
                'An error occurred while processing your request',
                500,
                ['message' => $e->getMessage()]
            );
        }
    }
}