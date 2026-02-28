<?php

declare(strict_types=1);

namespace App\Core;

use App\Logging\Logger;

class Application
{
    private Router $router;

    public function __construct()
    {
        $this->router = new Router();

        $routes = require __DIR__ . '/../../config/routes.php';

        foreach ($routes as $method => $methodRoutes) {
            foreach ($methodRoutes as $path => $handler) {
                $this->router->addRoute($method, $path, $handler);
            }
        }
    }

    public function handle(Request $request): Response
    {
        try {
            return $this->router->dispatch($request);
        } catch (\Exception $e) {
            Logger::error('Unhandled exception in router dispatch', ['exception' => $e]);
            return Response::error('An error occurred', 500);
        }
    }
}
